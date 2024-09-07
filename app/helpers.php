<?php


if (! function_exists('validate')) {
    function validate($request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = app('validator')->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            abort(422, $validator->errors()->first());
        }

        return $validator->validated();
    }
}
