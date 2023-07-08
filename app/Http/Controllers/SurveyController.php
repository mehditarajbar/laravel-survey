<?php

namespace App\Http\Controllers;

use App\Enums\SurveysQuestionEnum;
use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\SurveyAnswer;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate(2));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();
        if (isset($data['image'])) {
            $data['image'] = $this->saveImage($data['image']);
        }
        $survey = Survey::create($data);

        //Create New Question
        foreach ($data['questions'] as $question) {
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question);
        }
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     */
    public function show(Survey $survey, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            abort(403, 'Unauthorized action.');
        }
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     */
    public function showForGuest(Survey $survey)
    {
        return new SurveyResource($survey);
    }

    public function storeAnswer(StoreSurveyAnswerRequest $request, Survey $survey)
    {
        $validated = $request->validated();
        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s'),
        ]);

        foreach ($validated['answers'] as $questinId => $answer) {
            $question = SurveyQuestion::where(['id' => $questinId, 'survey_id' => $survey->id])->get();
            if (!$question) {
                return response("Invalid question ID : " . $questinId, ResponseStatusCode::HTTP_BAD_REQUEST);
            }

            $data = [
                'survey_question_id' => $questinId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer
            ];
            SurveyQuestionAnswer::create($data);

        }
        return response('',ResponseStatusCode::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();
        if (isset($data['image'])) {
            $data['image'] = $this->saveImage($data['image']);

            if ($survey->image) {
                File::delete(public_path($survey->image));
            }
        }
        $survey->update($data);

        //get Ids as plain array of existing questions
        $existingIds = $survey->questions()->pluck('id')->toArray();
        //get New ids
        $newIds = Arr::pluck($data['questions'], 'id');
        //find questions to delete
        $toDelete = array_diff($existingIds, $newIds);
        $toAdd = array_diff($newIds, $existingIds);
        SurveyQuestion::destroy($toDelete);
        foreach ($data['questions'] as $question) {
            if (in_array($question['id'], $toAdd)) {
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            }
        }

        $questionMap = collect($data['questions'])->keyBy('id');
        foreach ($survey->questions as $question) {
            if (isset($questionMap[$question->id])) {
                $this->updateQuestion($question, $questionMap[$question->id]);
            }
        }

        return new SurveyResource($survey);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $survey->delete();
        if ($survey->image) {
            File::delete(public_path($survey->image));
        }
        return response([
            '',
        ], ResponseStatusCode::HTTP_ACCEPTED);
    }

    private function saveImage($image): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            $image = substr($image, strpos($image, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'png', 'gif', 'jpeg'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);
            if ($image === false) {
                throw new \Exception('base64 decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }
        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);
        return $relativePath;

    }

    private function createQuestion($question)
    {
        if (is_array($question['data'])) {
            $question['data'] = json_encode($question['data']);
        }
        $validator = Validator::make($question, [
            'question' => ['required', 'string'],
            'type' => ['required', Rule::in(SurveysQuestionEnum::getType())],
            'description' => ['nullable', 'string'],
            'data' => ['present'],
            'survey_id' => ['exists:surveys,id']
        ]);

        return SurveyQuestion::create($validator->validated());
    }

    private function updateQuestion(SurveyQuestion $question, $data)
    {
        if (is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
            $validator = Validator::make($data, [
                'id' => ['exists:survey_questions,id'],
                'question' => ['required', 'string'],
                'type' => ['required', Rule::in(SurveysQuestionEnum::getType())],
                'description' => ['nullable', 'string'],
                'data' => ['present']
            ]);
            return $question->update($validator->validated());
        }
    }

}
