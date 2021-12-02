import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import os

# define universal range
complexity = ctrl.Antecedent(np.arange(0, 10.1, 0.1), 'complexity')
significance = ctrl.Antecedent(np.arange(0, 10.1, 0.1), 'significance')
relevance = ctrl.Antecedent(np.arange(0, 10.1, 0.1), 'relevance')
quality = ctrl.Consequent(np.arange(0.5, 1.51, 0.01), 'quality')

quality.defuzzify_method = 'centroid'  # 'centroid' || 'bisector' || 'mom' || 'som' || 'lom'

# gaussmf
###############
# define term and coord for them on the universal range
complexity['низька'] = fuzz.gaussmf(complexity.universe, 0, 2.5)
complexity['середня'] = fuzz.gaussmf(complexity.universe, 5, 1.3)
complexity['висока'] = fuzz.gaussmf(complexity.universe, 10, 2.5)

significance['низька'] = fuzz.gaussmf(significance.universe, 0, 2.5)
significance['середня'] = fuzz.gaussmf(significance.universe, 5, 1.3)
significance['висока'] = fuzz.gaussmf(significance.universe, 10, 2.5)

relevance['низька'] = fuzz.gaussmf(relevance.universe, 0, 2.5)
relevance['середня'] = fuzz.gaussmf(relevance.universe, 5, 1.3)
relevance['висока'] = fuzz.gaussmf(relevance.universe, 10, 2.5)

quality['Н'] = fuzz.gaussmf(quality.universe, 0.5, 0.12)
quality['НС'] = fuzz.gaussmf(quality.universe, 0.75, 0.07)
quality['С'] = fuzz.gaussmf(quality.universe, 1, 0.07)
quality['ВС'] = fuzz.gaussmf(quality.universe, 1.25, 0.07)
quality['В'] = fuzz.gaussmf(quality.universe, 1.5, 0.12)
###############

# define rules
rule1 = ctrl.Rule(complexity['низька'] & significance['низька'] & relevance['низька'],
                  quality['Н'])
rule2 = ctrl.Rule(complexity['низька'] & significance['низька'] & relevance['середня'],
                  quality['НС'])
rule3 = ctrl.Rule(complexity['низька'] & significance['низька'] & relevance['висока'],
                  quality['НС'])
rule4 = ctrl.Rule(complexity['низька'] & significance['середня'] & relevance['низька'],
                  quality['НС'])
rule5 = ctrl.Rule(complexity['низька'] & significance['середня'] & relevance['середня'],
                  quality['НС'])
rule6 = ctrl.Rule(complexity['низька'] & significance['середня'] & relevance['висока'],
                  quality['С'])
rule7 = ctrl.Rule(complexity['низька'] & significance['висока'] & relevance['низька'],
                  quality['НС'])
rule8 = ctrl.Rule(complexity['низька'] & significance['висока'] & relevance['середня'],
                  quality['С'])
rule9 = ctrl.Rule(complexity['низька'] & significance['висока'] & relevance['висока'],
                  quality['ВС'])

rule10 = ctrl.Rule(complexity['середня'] & significance['низька'] & relevance['низька'],
                   quality['НС'])
rule11 = ctrl.Rule(complexity['середня'] & significance['низька'] & relevance['середня'],
                   quality['НС'])
rule12 = ctrl.Rule(complexity['середня'] & significance['низька'] & relevance['висока'],
                   quality['С'])
rule13 = ctrl.Rule(complexity['середня'] & significance['середня'] & relevance['низька'],
                   quality['НС'])
rule14 = ctrl.Rule(complexity['середня'] & significance['середня'] & relevance['середня'],
                   quality['С'])
rule15 = ctrl.Rule(complexity['середня'] & significance['середня'] & relevance['висока'],
                   quality['ВС'])
rule16 = ctrl.Rule(complexity['середня'] & significance['висока'] & relevance['низька'],
                   quality['С'])
rule17 = ctrl.Rule(complexity['середня'] & significance['висока'] & relevance['середня'],
                   quality['ВС'])
rule18 = ctrl.Rule(complexity['середня'] & significance['висока'] & relevance['висока'],
                   quality['ВС'])

rule19 = ctrl.Rule(complexity['висока'] & significance['низька'] & relevance['низька'],
                   quality['НС'])
rule20 = ctrl.Rule(complexity['висока'] & significance['низька'] & relevance['середня'],
                   quality['С'])
rule21 = ctrl.Rule(complexity['висока'] & significance['низька'] & relevance['висока'],
                   quality['ВС'])
rule22 = ctrl.Rule(complexity['висока'] & significance['середня'] & relevance['низька'],
                   quality['С'])
rule23 = ctrl.Rule(complexity['висока'] & significance['середня'] & relevance['середня'],
                   quality['ВС'])
rule24 = ctrl.Rule(complexity['висока'] & significance['середня'] & relevance['висока'],
                   quality['ВС'])
rule25 = ctrl.Rule(complexity['висока'] & significance['висока'] & relevance['низька'],
                   quality['ВС'])
rule26 = ctrl.Rule(complexity['висока'] & significance['висока'] & relevance['середня'],
                   quality['ВС'])
rule27 = ctrl.Rule(complexity['висока'] & significance['висока'] & relevance['висока'],
                   quality['В'])

question_quality_ctrl = ctrl.ControlSystem([
    rule1, rule2, rule3, rule4, rule5, rule6, rule7, rule8, rule9,
    rule10, rule11, rule12, rule13, rule14, rule15, rule16, rule17, rule18,
    rule19, rule20, rule21, rule22, rule23, rule24, rule25, rule26, rule27
])

question_quality = ctrl.ControlSystemSimulation(question_quality_ctrl)

question_quality.input['complexity'] = int(os.environ['complexity'])
question_quality.input['significance'] = int(os.environ['significance'])
question_quality.input['relevance'] = int(os.environ['relevance'])

question_quality.compute()

print(question_quality.output['quality'])
