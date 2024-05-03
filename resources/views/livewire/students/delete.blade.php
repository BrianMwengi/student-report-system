<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Student;
use App\Models\Exam;

new class extends Component {
    public $student_id;

    #[On('deleteStudent')]
    public function deleteStudent($id)
    {
        $this->student_id = $id;

        // Find and delete the related exam records
        Exam::where('student_id', $this->student_id)->delete();

        // Find the student
        $student = Student::find($this->student_id);

        // Check if the student exists and delete
        if ($student) {
            $student->delete();
        }

        // Dispatch an event to refresh the student list and pass the message
        $this->dispatch('studentDeleted', 'Student deleted successfully.');
    }
}; ?>

<div>
   //
</div>