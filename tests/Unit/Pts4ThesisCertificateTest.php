<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use App\Models\Thesis;
use App\Models\Pts4Form;
use App\Services\PtsDocumentService;
use Illuminate\Support\Facades\Storage;

class Pts4ThesisCertificateTest extends TestCase
{
    public function test_thesis_certificate_pdf_view_renders_cleanly(): void
    {
        $html = view('pdf.thesis_certificate', [
            'rollNumber' => 'TEST9999',
            'studentName' => 'Test Student',
            'departmentName' => 'Department of Physics',
            'thesisTitle' => 'Quantum Simulation Study',
            'submissionDate' => '10 August 2026',
            'issueDate' => '20 August 2026',
            'logoBase64' => '',
        ])->render();

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('TO WHOMSOEVER IT MAY CONCERN', $html);
        $this->assertStringContainsString('TEST9999', $html);
        $this->assertStringContainsString('Quantum Simulation Study', $html);
    }

    public function test_pts_doc_service_generates_and_stores_certificate(): void
    {
        Storage::fake('local');

        $dept = new Department(['name' => 'Physics', 'code' => 'PHY']);
        $user = new User(['name' => 'John Doe', 'email' => 'john@test.com', 'role' => 'student']);
        
        $student = new Student([
            'roll_number' => '2100000001',
            'date_registration' => '2023-08-01',
            'date_joining' => '2023-08-01',
        ]);
        $student->id = 99;
        $student->setRelation('user', $user);
        $student->setRelation('department', $dept);

        $thesis = new Thesis(['title' => 'Analysis of Quantum Transitions']);
        $thesis->id = 101;
        $thesis->setRelation('student', $student);

        $pts4 = new Pts4Form([
            'thesis_id' => 101,
            'thesis_title' => 'Analysis of Quantum Transitions',
            'thesis_doc_path' => 'students/2100000001/thesis_101/pts4/approved/2100000001_PTS4_Thesis_Student.pdf',
            'status' => 'approved',
            'current_stage' => 'completed',
        ]);
        $pts4->id = 55;
        $pts4->setRelation('thesis', $thesis);

        $service = new PtsDocumentService();
        $storedPath = $service->generateThesisCertificate($pts4);

        $this->assertNotEmpty($storedPath);
        $this->assertTrue(Storage::disk('local')->exists($storedPath));
        $this->assertStringEndsWith('2100000001_Thesiscerificate.pdf', $storedPath);

        $content = Storage::disk('local')->get($storedPath);
        $this->assertStringStartsWith('%PDF', $content);
    }
}
