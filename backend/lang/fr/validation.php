<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'unique' => 'Cette :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'array' => 'Le champ :attribute doit être un tableau.',

    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'file' => 'Le fichier :attribute doit faire au moins :min kilo-octets.',
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
    ],

    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
        'file' => 'Le fichier :attribute ne peut pas dépasser :max kilo-octets.',
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
    ],

    // Noms lisibles des champs (remplacent :attribute)
    'attributes' => [
        'name' => 'nom',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'file' => 'fichier',
        'expires_in_days' => "durée d'expiration",
        'tags' => 'tags',
    ],
];
