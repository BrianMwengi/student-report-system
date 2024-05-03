<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\Exam;

new class extends Component {
    public $student_id;

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
    <script>
        window.addEventListener('show-delete-confirmation', function(event) {
            let id = event.detail.id;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('deleteStudent', id);
                }
            });
        });
    </script>
</div>
