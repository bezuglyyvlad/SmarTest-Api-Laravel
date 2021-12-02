<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerDestroyRequest;
use App\Http\Requests\AnswerIndexRequest;
use App\Http\Requests\AnswerStoreRequest;
use App\Http\Requests\AnswerUpdateRequest;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\PrivateAnswerResource;
use App\Models\Answer;
use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AnswerIndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(AnswerIndexRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $question_id = $request->validated()['question_id'];
        return PrivateAnswerResource::collection(
            Answer
                ::where('question_id', $question_id)
                ->orderByDesc('updated_at')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AnswerStoreRequest $request
     * @return PrivateAnswerResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(AnswerStoreRequest $request): PrivateAnswerResource
    {
        // deny if users passing test or expert test is published
        $question = Question::findOrFail($request->question_id);
        $activeTestIds = Test::getActiveTestIdsByExpertTest($question->expert_test_id);
        ExpertTest::findOrFail($question->expert_test_id)->validateExpertTestNotPublished();
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        $createdAnswer = Answer::create($request->validated());

        return new PrivateAnswerResource($createdAnswer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AnswerUpdateRequest $request
     * @param Answer $answer
     * @return PrivateAnswerResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(AnswerUpdateRequest $request, Answer $answer): PrivateAnswerResource
    {
        $validatedAnswer = $request->validated();
        $oldAnswer = $answer->replicate();
        $newAnswer = $answer->replicate();

        // if the data not changed, return the old resource
        if ($newAnswer->fill($validatedAnswer) == $oldAnswer) {
            return new PrivateAnswerResource($answer);
        }

        // deny if users passing test or expert test is published
        $question = Question::findOrFail($answer->question_id);
        $activeTestIds = Test::getActiveTestIdsByExpertTest($question->expert_test_id);
        ExpertTest::findOrFail($question->expert_test_id)->validateExpertTestNotPublished();
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        DB::transaction(function () use ($newAnswer, $answer) {
            $answer->delete();

            $newAnswer->modified_records_parent_id = $answer->id;
            $newAnswer->save();
        });

        return new PrivateAnswerResource($newAnswer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AnswerDestroyRequest $request
     * @param Answer $answer
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function destroy(AnswerDestroyRequest $request, Answer $answer)
    {
        $countQuestionAnswers = Answer::where('question_id', $answer->question_id)->count();
        if ($countQuestionAnswers <= 2) {
            throw ValidationException::withMessages([
                'question_id' => [
                    'Кількість відповідей не може бути менше 2.'
                ]
            ]);
        }

        // deny if users passing test or expert test is published
        $question = Question::findOrFail($answer->question_id);
        $activeTestIds = Test::getActiveTestIdsByExpertTest($question->expert_test_id);
        ExpertTest::findOrFail($question->expert_test_id)->validateExpertTestNotPublished();
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        $answer->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
