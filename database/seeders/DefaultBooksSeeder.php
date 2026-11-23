<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Support\Api;
use Illuminate\Database\Seeder;

/** The two reference books the original site shipped with (idempotent). The PDF/cover files live in public/books/. */
class DefaultBooksSeeder extends Seeder
{
    public function run(): void
    {
        $now = Api::nowIso();

        Book::firstOrCreate(['slug' => 'grammaire-progressive-du-fran-ais-a2-b1-'], [
            'title' => 'Grammaire progressive du français – Intermédiaire (A2/B1)',
            'author' => 'Maïa Grégoire, Odile Thiévenaz (CLE International)',
            'level' => 'A2–B1', 'pages' => 282, 'category' => 'Grammar',
            'cover' => '/books/grammaire-progressive-du-fran-ais-a2-b1-.jpg',
            'file_url' => '/books/Grammaire%20progressive%20du%20fran%C3%A7ais%20A2-B1%20Niveau%20interm%C3%A9diaire.pdf',
            'file_name' => 'Grammaire progressive du français A2-B1 Niveau intermédiaire.pdf',
            'blurb' => '52 short units with rules, exercises and tests, from être to the subjunctive. Our grammar topics follow its order.',
            'blurb_hi' => '52 छोटे यूनिट: नियम, अभ्यास और टेस्ट — être से subjonctif तक। हमारे grammar topics इसी क्रम पर हैं।',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        Book::firstOrCreate(['slug' => 'pdfcoffee-com-english-grammar-for-studen'], [
            'title' => 'English Grammar for Students of French',
            'author' => 'Jacqueline Morton (Olivia and Hill Press)',
            'level' => 'A1–B2', 'pages' => 191, 'category' => 'Grammar',
            'cover' => '/books/pdfcoffee-com-english-grammar-for-studen.jpg',
            'file_url' => '/books/pdfcoffee.com_english-grammar-for-students-of-french-1993-morton-pdf-free.pdf',
            'file_name' => 'pdfcoffee.com_english-grammar-for-students-of-french-1993-morton-pdf-free.pdf',
            'blurb' => 'Explains grammar terms (gender, tense, mood, pronouns) in English first, then shows how French works. Ideal if the terminology confuses you.',
            'blurb_hi' => 'पहले अंग्रेज़ी में grammar के शब्द समझाती है, फिर फ़्रेंच से तुलना करती है।',
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }
}
