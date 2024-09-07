<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class CrudController extends Controller
{
    protected $fields;

    protected $includes;

    protected $filters;

    protected $limit = 25;

    protected $sort;

    protected $page;

    protected $model;

    protected $resource;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $segments = $request->segments();

        if (empty($segments)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("");
        }

        $method = (@$segments[1] == "search") ? "GET" : $request->method();

        if (!$this->methodAllowed($segments[0], $method)) {
            throw new \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException([]);
        }

        $this->model = $this->setModel($segments[0]);

        // Check Model exists
        if (!class_exists($this->model)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("");
        }

        $this->resource = $segments[0];
    
        $this->page = $request->input('page') ? $request->input('page') : 1;
    }

    private function methodAllowed($resource = '', $method = 'GET')
    {
        // GET,PUT,POST,DELETE
        $rules = config('crud');
        return (isset($rules[$resource]) && strpos($rules[$resource], $method) !== false) ? true : false;
    }


    private function setModel($model)
    {
        $getModelName = function ($model) {
            $modelName = '';
            $words     = explode('_', $model);
            foreach ($words as &$word) {
                $lastLetter = substr($word, strlen($word) - 1, strlen($word));
                if ('s' == $lastLetter && 'sms' != $word && 'epos' != $word) {
                    $word = substr($word, 0, -1);
                }

                $modelName .= ucfirst($word);
            }

            return "App\\Models\\" . $modelName;
        };

        $modelName = $getModelName($model);

        return $modelName;
    }

        /**
     * Get Record
     *
     * @return boolean
     */

     public function index(Request $request, $id = 0)
     {            
         $response = [];
 
         $model = new $this->model;
 
         $input = $request->except(['api_token']);
         // Limit the results to be displayed
         $this->setLimit($input);
         // Set fields to display (&fields=x,y,z)
         $this->setFields($input);
         // Set nested includes to display (&includes=x,y,z)
         $this->setIncludes($input);
         //Set sort
         $this->setSort($input);
         // Set fields to display (&x=1&y=2&z=3)
         $this->setFilters($input);
         $mergeOrder = false;
         try {
             // Create an instance of the query builder
             $query = $model->select($this->fields);
             // Handle ID's if passed.
             $ids = explode(',', $id);
             $key = $model->getKeyName();
             if (!empty($ids) && !empty($ids[0])) {
                 $query->where(function ($query) use ($key, $ids) {
                     foreach ($ids as $id) {
                         $query->orWhere($key, '=', $id);
                     }
                 });
             }
             if (!empty($this->includes)) {
                 foreach ($this->includes as $include => $value) {
                         $query->with($value);
                 }
             }
 
             // Filter results
             if (!empty($this->filters)) {
                 foreach ($this->filters as $filter => $value) {
                     // Handle array, IN & NOT IN
                     if (is_array($value)) {
                         $equal    = [];
                         $notEqual = [];
                         foreach ($value as $columnValue) {
                             if ("!" != $columnValue[0]) {
                                 $equal[] = $columnValue;
                             } else {
                                 $notEqual[] = ltrim($columnValue, $columnValue[0]);
                             }
                         }
                         if (count($equal)) {
                             $query->whereIn($filter, $equal);
                         }
                         if (count($notEqual)) {
                             $query->whereNotIn($filter, $notEqual);
                         }
                     } else {
                         if (Str::contains($value, "|") !== false) {
                             $dates          = explode("|", $value);
                             $dateTimeFormat = 'Y-m-d H:i:s';
                             $format         = 'Y-m-d';
 
                             if (
                                 $dates[0] && '' != $dates[0] &&
                                 Str::contains($dates[0], ':')
                             ) {
                                 $dates[0] = makeDateCarbon($dateTimeFormat, $dates[0], env('APP_TIMEZONE'), date('P'));
                             } elseif ($dates[0] && '' != $dates[0]) {
                                 $dates[0] = makeDateCarbon($format, $dates[0], env('APP_TIMEZONE'), date('P'));
                             }
 
                             if (
                                 $dates[1] && '' != $dates[1] &&
                                 Str::contains($dates[1], ':')
                             ) {
                                 $dates[1] = makeDateCarbon($dateTimeFormat, $dates[1], env('APP_TIMEZONE'), date('P'));
                             } elseif ($dates[1] && '' != $dates[1]) {
                                 $dates[1] = makeDateCarbon($format, $dates[1], env('APP_TIMEZONE'), date('P'));
                             }
 
                             if ('' != $dates[0] || '' != $dates[1]) {
                                 $startDateTime = \DateTime::createFromFormat($dateTimeFormat, $dates[0]);
                                 $endDateTime   = \DateTime::createFromFormat($dateTimeFormat, $dates[1]);
 
                                 $startDate = \DateTime::createFromFormat($format, $dates[0]);
                                 $endDate   = \DateTime::createFromFormat($format, $dates[1]);
                                 if (
                                     $startDateTime && $startDateTime->format($dateTimeFormat) == $dates[0]
                                     &&
                                     $endDateTime && $endDateTime->format($dateTimeFormat) == $dates[1]
                                 ) {
                                     $query->whereBetween($filter, [$dates[0], $dates[1]]);
                                 } elseif (
                                     $startDate && $startDate->format($format) == $dates[0]
                                     &&
                                     $endDate && $endDate->format($format) == $dates[1]
                                 ) {
                                     $query->whereBetween($filter, [$dates[0], $dates[1]]);
                                 } elseif (
                                     (($startDate && $startDate->format($format) == $dates[0])
                                         ||
                                         ($startDateTime && $startDateTime->format($dateTimeFormat) == $dates[0]))
                                     && '' == $dates[1]) {
                                     $query->where($filter, '>=', $dates[0]);
                                 } elseif (
                                     (($endDate && $endDate->format($format) == $dates[1])
                                         ||
                                         ($endDateTime && $endDateTime->format($dateTimeFormat) == $dates[1]))
                                     && '' == $dates[0]) {
                                     $query->where($filter, '<=', $dates[1]);
                                 }
                                 if (
                                     is_numeric($dates[0]) && is_numeric($dates[1])
                                 ) {
                                     $query->whereBetween($filter, [$dates[0], $dates[1]]);
                                 }
                             }
                         } elseif ("!" == $value[0]) {
                             $query->where($filter, '<>', ltrim($value, $value[0]));
                         } elseif (\DateTime::createFromFormat('Y-m-d', $value) !== false) {
                             $query->whereDate($filter, $value);
                         } else {
                             $query->where($filter, '=', $value);
                         }
                     }
                 }
             }
             //Sorting results
             $this->sortResults($query, $model);
 
             // Remove page get variable if present
             $fullUrl = preg_replace("/&page\=[0-9]+/", '', $request->fullUrl());
             
             // Order result by
             if (empty($id)) {
                 $response = $query->paginate($this->limit[0], ['*'], 'page', $this->page)
                 ->setPath($fullUrl);
             } else {
                 $response['data'] = $query->get();
             }

         } catch (\Exception $ex) {
             throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Cannot process request, probably bad field passed.");
         }
         
         
         if ($mergeOrder) {
             $response = ('consumer_request' == $this->resource) ? $model->getOrder($response) : $response;
         }
         return response()->json($response);
     }

     protected function setLimit(&$input)
     {
         $this->setParams($input, 'page');
 
         return $this->setParams($input, 'limit', 10);
     }
 
     protected function setParams(&$input, $param, $default = '')
     {
         $outcome = false;
         // Assigning an empty array could cause some problems
         // So null it.
         if (!empty($default)) {
             $this->$param = [$default];
         } else {
             $this->$param = null;
         }
 
         if (!empty($input[$param])) {
             $this->$param = explode(',', $input[$param]);
             $outcome      = true;
         }
         unset($input[$param]);
 
         return $outcome;
     }
 
     protected function setFields(&$input)
     {
         $this->setParams($input, 'fields', '*');
     }
 
     protected function setIncludes(&$input)
     {
         $this->setParams($input, 'includes');
     }
 
     protected function setSort(&$input)
     {
         $this->setParams($input, 'sort', "desc");
     }
 
     protected function setFilters(&$input)
     {
         // Handle custom queries
         if (!empty($input)) {
             foreach ($input as $filter => $value) {
                 $this->filters[$filter] = $value;
             }
         }
     }

         /**
     * Helper functions
     */
    private function sortResults(&$query, $model)
    {
        if ('desc' == $this->sort[0]) {
            $query->orderBy($model->getKeyName(), $this->sort[0]);
        } elseif (substr($this->sort[0], 0, 1) === '-') {
            $str = ltrim($this->sort[0], '-');
            $query->orderBy($str, 'asc');
        } else {
            $query->orderBy($this->sort[0], 'desc');
        }
    }

        /**
     * Add Record
     *
     * @return boolean
     */
    public function store(Request $request)
    {
        $response = [];
        $model = new $this->model;

        $input = $request->all();
        if (empty($input)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Request data empty, nothing to create.");
        }

        // $validator = \Validator::make($input, $model->getRules($model, 'create'));
        // if (!$validator->passes()) {
        //     throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Validation failed '. $validator->errors());
        // }   
        $object = new $model;
        if (is_null($object)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Failed to create instance of model');
        }

        // Identify duplicate
        if (method_exists($object, "hasDuplicate") && $object->hasDuplicate($input)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException($this->getFriendlyDuplicateMessage($object));
        }
        $resource = [];
        try {
            $resource = $object->insert($input);
        } catch (\PDOException $e) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("failed to create");
        }
        return response()->json(['outcome' => 'success']);
    }

    public function getFriendlyDuplicateMessage($object)
    {
        // Convert the resource to a friendly singular name
        $resource = str_replace('_', ' ', $this->resource);
        $resource = (substr($resource, strlen($resource) - 1, strlen($resource)) == 's')
        ? substr($resource, 0, strlen($resource) - 1)
        : $resource;

        $message = "A " . ucwords($resource) . " with the specified ";
        foreach ($object->duplicateColumns as $column) {
            $column = str_replace("_", " ", $column);
            $message .= $column . ' and ';
        }
        $message = substr($message, 0, strlen($message) - 5);
        $message .= " already exists.";

        return $message;
    }

    /**
     * Update Record
     *
     * @return boolean
     */
    public function update(Request $request, $id = 0)
    {
        $response = [];

        $model = new $this->model;
        $input = $request->all();

        if (empty($input)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Request data empty, nothing to update");
        }

        // $updateRules = $model->getRules($model, 'update');
        // // Validate request
        //    $validator = \Validator::make($input,$updateRules);
        // if (!$validator->passes()) {
        //     throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Validation failed'. $validator->errors());
        // }

        // Search and pull record
        $object = $model::where($model->getKeyName(), $id)->first();
        if (is_null($object)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Resource not found");
        }
        
        // Identify duplicate
        if (method_exists($object, "hasDuplicate") && $object->hasDuplicate($input, $id)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('duplication '. $validator->errors());

        }

        // Check the column exists
        $errors = [];
        foreach ($input as $column => $value) {
            $connectionName = $object->getConnectionName();
        }

        $connectionName = (empty($connectionName)) ? "mysql" : $connectionName;
        if (!\Schema::connection($connectionName)->hasColumn($object->getTable(), $column)) {
            $errors[] = "`$column` is not a valid field";
        }

        if (!empty($errors)) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Request contains invalid fields ". $errors);
        }

        // Perform the mass update (will respect rules)
        try {
            $object->where('id', $id)->update($input); 
        } catch (\PDOException $e) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("failed to update");
        }
        $response = ['outcome' => 'success'];

        return response()->json($response);
    }


    /**
     * Delete Record
     *
     * @return boolean
     */
    public function delete(Request $request, $id = 0)
    {
        $response = [];

        $model = new $this->model;
        if (empty($id)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("No resource #id given");
        }

        // Search and pull record
        $object = $model::where($model->getKeyName(), $id)->first();
        if (is_null($object)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Resource not found");
        }
        // Perform the delete
        try {
            $object->where('id', $id)->delete();
        } catch (\PDOException $e) {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException("Failed to delete");
        }
        $response = ['outcome' => 'success'];

        return response()->json($response);
    }

}
