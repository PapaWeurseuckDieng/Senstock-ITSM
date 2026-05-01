<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|min:5|max:255',
            'description'  => 'required|string|min:10',
            'type'         => 'required|in:incident,demande,panne,changement',
            'priority'     => 'required|in:critique,haute,normale,faible',
            'category'     => 'required|in:materiel,logiciel,reseau,acces,autre',
            'attachments'  => 'nullable|array|max:5',
            'attachments.*'=> 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,txt,zip',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Le titre est obligatoire.',
            'title.min'            => 'Le titre doit contenir au moins 5 caractères.',
            'description.required' => 'La description est obligatoire.',
            'description.min'      => 'La description doit contenir au moins 10 caractères.',
            'type.required'        => 'Le type de ticket est obligatoire.',
            'type.in'              => 'Le type sélectionné est invalide.',
            'priority.required'    => 'La priorité est obligatoire.',
            'priority.in'          => 'La priorité sélectionnée est invalide.',
            'category.required'    => 'La catégorie est obligatoire.',
            'attachments.*.max'    => 'Chaque pièce jointe ne doit pas dépasser 10 Mo.',
            'attachments.*.mimes'  => 'Format de fichier non autorisé.',
        ];
    }
}
