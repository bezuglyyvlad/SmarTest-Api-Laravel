<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Image;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Http\Requests\QuestionUpdateRequest;
use App\Http\Requests\QuestionUploadImageRequest;
use App\Http\Resources\PrivateQuestionResource;
use App\Http\Resources\QuestionResource;
use App\Models\Answer;
use App\Models\ExpertTest;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class QuestionController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param QuestionRequest $request
     * @param Question $question
     * @return PrivateQuestionResource
     */
    public function show(QuestionRequest $request, Question $question): PrivateQuestionResource
    {
        return new PrivateQuestionResource($question);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param QuestionUpdateRequest $request
     * @param Question $question
     * @return PrivateQuestionResource
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function update(QuestionUpdateRequest $request, Question $question): PrivateQuestionResource
    {
        $validatedQuestion = $request->validated();
        $oldQuestion = $question->replicate();
        $newQuestion = $question->replicate();

        // if the data not changed, return the old resource
        if ($newQuestion->fill($validatedQuestion) == $oldQuestion) {
            return new PrivateQuestionResource($question);
        }

        // deny if users passing test or expert test is published
        $activeTestIds = Test::getActiveTestIdsByExpertTest($question->expert_test_id);
        ExpertTest::findOrFail($question->expert_test_id)->validateExpertTestNotPublished();
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        $newQuestion->quality_coef = $newQuestion->getQualityCoefByFuzzyLogic();

        DB::transaction(function () use ($question, $newQuestion) {
            self::updateRecord($question, $newQuestion);
        });

        return new PrivateQuestionResource($newQuestion);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param QuestionRequest $request
     * @param Question $question
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function destroy(QuestionRequest $request, Question $question): \Illuminate\Http\Response
    {
        // deny if users passing test or expert test is published
        $activeTestIds = Test::getActiveTestIdsByExpertTest($question->expert_test_id);
        ExpertTest::findOrFail($question->expert_test_id)->validateExpertTestNotPublished();
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        DB::transaction(function () use ($question) {
            $answerIds = Answer::where('question_id', $question->id)->pluck('id');

            $question->delete();
            Answer::destroy($answerIds);
        });

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param QuestionUploadImageRequest $request
     * @param Question $question
     * @return PrivateQuestionResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function uploadImage(QuestionUploadImageRequest $request, Question $question)
    {
        // deny if users passing test or expert test is published
        $activeTestIds = Test::getActiveTestIdsByExpertTest($question->expert_test_id);
        ExpertTest::findOrFail($question->expert_test_id)->validateExpertTestNotPublished();
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        $newQuestion = $question->replicate();
        $imageFileName = Image::saveImage('question', $request->file('image'));
        if ($imageFileName !== '') {
            $newQuestion->image = $imageFileName;
            DB::beginTransaction();
            try {
                self::updateRecord($question, $newQuestion);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Image::deleteImage('question', $imageFileName);
                throw $e;
            }
            return new PrivateQuestionResource($newQuestion);
        } else {
            return response('Не вдалося завантажити.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param QuestionRequest $request
     * @param Question $question
     * @return PrivateQuestionResource
     * @throws \Throwable
     */
    public function deleteImage(QuestionRequest $request, Question $question): PrivateQuestionResource
    {
        // deny if users passing test or expert test is published
        $activeTestIds = Test::getActiveTestIdsByExpertTest($question->expert_test_id);
        ExpertTest::findOrFail($question->expert_test_id)->validateExpertTestNotPublished();
        Test::validateNobodyPassesExpertTest($activeTestIds->count() > 0);

        $newQuestion = $question->replicate();
        $newQuestion->image = null;

        DB::transaction(function () use ($question, $newQuestion) {
            self::updateRecord($question, $newQuestion);
        });

        return new PrivateQuestionResource($newQuestion);
    }

    private static function updateRecord($oldQuestion, $newQuestion)
    {
        $oldQuestion->delete();

        $newQuestion->modified_records_parent_id = $oldQuestion->id;
        $newQuestion->save();

        // update question_id in Answer table
        Answer::where('question_id', $oldQuestion->id)
            ->update(['question_id' => $newQuestion->id]);
    }
}
