import pandas as pd
import os
import json


def reorder_columns(dataframe, col_name, position):
    temp_col = dataframe[col_name]
    dataframe = dataframe.drop(columns=[col_name])
    dataframe.insert(loc=position, column=col_name, value=temp_col)
    return dataframe


df_list = []
for i in range(int(os.environ['count'])):
    df_list.append(pd.DataFrame.from_dict(json.loads(os.environ[str(i)])))

df = pd.concat(df_list, sort=False, axis=0)
df.reset_index(drop=True, inplace=True)

# data preparation
df['start_date'] = pd.to_datetime(df['start_date'], utc=True)
df['finish_date'] = pd.to_datetime(df['finish_date'], utc=True)
df['spent_time(m)'] = (df['finish_date'] - df['start_date']).dt.total_seconds() // 60

df.drop(columns=['id'], inplace=True)

df = reorder_columns(df, 'user_email', 0)
df = reorder_columns(df, 'expert_test_title', 1)
df = reorder_columns(df, 'spent_time(m)', 4)
df = reorder_columns(df, 'number_of_test_passes', 5)

# score by year
df_score_by_year = df.copy()
df_score_by_year['start_date'] = df_score_by_year['start_date'].dt.year
df_score_by_year = df_score_by_year.groupby('start_date').agg({'score': ['median']})
df_score_by_year.columns = ["_".join(x) for x in df_score_by_year.columns]

df_questions = (df
                .filter(regex=r"^Q")
                .dropna(axis=1, how="all"))
numberOfQuestions = df_questions.count(axis=1)

# score, spent_time(m) by number_of_test_passes
df_by_number_of_test_passes = df.groupby(['number_of_test_passes']).agg(
    {'score': ['median'], 'spent_time(m)': ['median']}
)
df_by_number_of_test_passes.columns = ["_".join(x) for x in df_by_number_of_test_passes.columns]

print(json.dumps({
    # descriptive statistics
    'shape': json.dumps(df.shape),
    'head': df.head().to_json(date_format='iso'),
    'describe': df.describe(include="all", datetime_is_numeric=True).to_json(date_format='iso'),

    'count_of_tests': int(df['expert_test_title'].count()),

    # score, spent_time(m), number_of_test_passes hist
    'score_hist': df["score"].to_json(),
    'spent_time(m)_hist': df["spent_time(m)"].to_json(),
    'number_of_test_passes_hist': df["number_of_test_passes"].to_json(),

    # score by year
    'score_by_year': df_score_by_year.to_json(date_format='iso'),

    # questions
    'questions': df_questions.to_json(),

    # number of questions
    'min_number_of_questions': int(numberOfQuestions.min()),
    'max_number_of_questions': int(numberOfQuestions.max()),

    # score, spent_time(m) by number_of_test_passes
    'data_by_number_of_test_passes': df_by_number_of_test_passes.to_json(),

    # question correlation
    'question_correlation': df_questions.corr(method="pearson").to_json()
}))
