import pandas as pd
import numpy as np
import os
import json


def reorder_columns(dataframe, col_name, position):
    temp_col = dataframe[col_name]
    dataframe = dataframe.drop(columns=[col_name])
    dataframe.insert(loc=position, column=col_name, value=temp_col)
    return dataframe


def filter_not_outliers(series):
    iqr = (series.quantile(0.75) - series.quantile(0.25))
    max_value = (series.quantile(0.75) + iqr * 1.5)
    min_value = (series.quantile(0.25) - iqr * 1.5)
    return (series < min_value) | (series > max_value)


df_list = []
for i in range(int(os.environ['count'])):
    df_list.append(pd.DataFrame.from_dict(json.loads(os.environ[str(i)])))

df = pd.concat(df_list, sort=False, axis=0)
df.reset_index(drop=True, inplace=True)

df_questions = (df
                .filter(regex=r"^Q")
                .dropna(axis=1, how="all"))

question_hists = {}
for column in df_questions.columns:
    count, division = np.histogram(df_questions[column].dropna())
    question_hists[column] = [count.tolist(), division.tolist()]
numberOfQuestions = df_questions.count(axis=1)

# data preparation
df['start_date'] = pd.to_datetime(df['start_date'], utc=True)
df['finish_date'] = pd.to_datetime(df['finish_date'], utc=True)
df['spent_time_m'] = (df['finish_date'] - df['start_date']).dt.total_seconds() // 60
df['number_of_questions'] = df_questions.count(axis=1)

df.drop(columns=['id'], inplace=True)

df = reorder_columns(df, 'user_email', 0)
df = reorder_columns(df, 'expert_test_title', 1)
df = reorder_columns(df, 'spent_time_m', 4)
df = reorder_columns(df, 'number_of_test_passes', 5)
df = reorder_columns(df, 'number_of_questions', 6)

# score hist
score_hist_count, score_hist_division = np.histogram(df["score"])
# score hist
spent_time_hist_count, spent_time_hist_division = np.histogram(df["spent_time_m"])
# score hist
number_of_test_passes_hist_count, number_of_test_passes_hist_division = np.histogram(
    df["number_of_test_passes"], bins=df["number_of_test_passes"].max()
)

# score by year
df_score_by_year = df.copy()
df_score_by_year['start_date'] = df_score_by_year['start_date'].dt.year
df_score_by_year = df_score_by_year.groupby('start_date').agg({'score': ['median']})
df_score_by_year.columns = ["_".join(x) for x in df_score_by_year.columns]

# score, spent_time_m by number_of_test_passes
df_by_number_of_test_passes = df.groupby(['number_of_test_passes']).agg(
    {'score': ['median'], 'spent_time_m': ['median']}
)
df_by_number_of_test_passes.columns = ["_".join(x) for x in df_by_number_of_test_passes.columns]

print(json.dumps({
    # descriptive statistics
    'shape': json.dumps(df.shape),
    'data_frame': df.to_json(date_format='iso', orient='table'),
    'data_frame_describe': df.describe(include="all", datetime_is_numeric=True).to_json(
        date_format='iso', orient='table'),

    # score, spent_time_m, number_of_test_passes hist
    'score_hist_count': json.dumps(score_hist_count.tolist()),
    'score_hist_division': json.dumps(score_hist_division.tolist()),
    'spent_time_m_hist_count': json.dumps(spent_time_hist_count.tolist()),
    'spent_time_m_hist_division': json.dumps(spent_time_hist_division.tolist()),
    'number_of_test_passes_hist_count': json.dumps(number_of_test_passes_hist_count.tolist()),
    'number_of_test_passes_hist_division': json.dumps(
        number_of_test_passes_hist_division.tolist()),

    # score by year
    'score_by_year': df_score_by_year.to_json(date_format='iso', orient='split'),

    # questions
    'questions_describe': df_questions.describe().to_json(),
    'question_outliers': df_questions.apply(
        lambda x: x.dropna()[filter_not_outliers].unique().tolist(),
        axis=0).to_json(),
    'question_hists': json.dumps(question_hists),

    # number of questions
    'number_of_questions_describe': df['number_of_questions'].describe().to_json(),
    'number_of_questions_outliers':
        json.dumps(df['number_of_questions'][filter_not_outliers].unique().tolist()),

    # score, spent_time_m by number_of_test_passes
    'data_by_number_of_test_passes': df_by_number_of_test_passes.to_json(),

    # question correlation
    'question_correlation': df_questions.corr(method="pearson").to_json()
}))
