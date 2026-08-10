<?php

$credentialsPath = env('FIREBASE_CREDENTIALS', 'storage/app/firebase/service-account.json');

$isAbsolute = str_starts_with($credentialsPath, '/') || preg_match('#^[A-Za-z]:[\\\\/]#', $credentialsPath);

return [
    'credentials' => $isAbsolute ? $credentialsPath : base_path($credentialsPath),
    'project_id' => env('FIREBASE_PROJECT_ID'),
];
