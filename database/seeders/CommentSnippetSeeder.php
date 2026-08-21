<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommentSnippet;

class CommentSnippetSeeder extends Seeder
{
    // Run the database seeds.
    public function run(): void
    {
        $snippets = [
            // General / All Forms
            [
                'content' => "I have verified all student details, academic records, and attached documentation. Forwarded with recommendation for further processing.",
                'form_type' => 'all',
                'comment_type' => 'verification_remark',
                'role' => 'section_officer',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'content' => "All prerequisite departmental requirements, registration history, and fee clearances have been thoroughly verified and found to be complete.",
                'form_type' => 'all',
                'comment_type' => 'verification_remark',
                'role' => 'section_officer',
                'sort_order' => 2,
                'is_active' => true,
            ],
            // PTS-1 Specific
            [
                'content' => "I have verified the candidate's coursework completion status, credit requirements, and attached documentation. Everything is found to be in order.",
                'form_type' => 'pts1',
                'comment_type' => 'verification_remark',
                'role' => 'section_officer',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'content' => "Verified that the publication list, supervisor endorsements, and comprehensive examination clearance are compliant with PhD guidelines.",
                'form_type' => 'pts1',
                'comment_type' => 'verification_remark',
                'role' => 'section_officer',
                'sort_order' => 11,
                'is_active' => true,
            ],
            // PTS-2 Extension Specific
            [
                'content' => "Verified the extension request timeline, open seminar date, and supporting justifications. Recommended for forwarding to DOAA.",
                'form_type' => 'pts2_extension',
                'comment_type' => 'verification_remark',
                'role' => 'section_officer',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'content' => "Checked candidate's seminar date and requested extension window. Extension duration is within permitted academic limits.",
                'form_type' => 'pts2_extension',
                'comment_type' => 'verification_remark',
                'role' => 'section_officer',
                'sort_order' => 21,
                'is_active' => true,
            ],
            // PTS-2 Specific
            [
                'content' => "I have verified all student details, academic records, open seminar date, and attached synopsis documentation for this PTS-2 submission.",
                'form_type' => 'pts2',
                'comment_type' => 'verification_remark',
                'role' => 'academic_office',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'content' => "Course credits and academic requirements have been verified against departmental records. Everything is found to be in order.",
                'form_type' => 'pts2',
                'comment_type' => 'verification_remark',
                'role' => 'academic_office',
                'sort_order' => 31,
                'is_active' => true,
            ],
            [
                'content' => "Verified synopsis submission prerequisites, fee clearances, and examination committee details. Forwarded for DOAA approval.",
                'form_type' => 'pts2',
                'comment_type' => 'verification_remark',
                'role' => 'academic_office',
                'sort_order' => 32,
                'is_active' => true,
            ],
        ];

        foreach ($snippets as $snippet) {
            CommentSnippet::firstOrCreate(
                [
                    'content' => $snippet['content'],
                    'form_type' => $snippet['form_type'],
                    'role' => $snippet['role'],
                ],
                $snippet
            );
        }
    }
}
