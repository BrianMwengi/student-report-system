<x-app-layout>
    <div class="container mx-auto mt-5">
        <div class="max-w-3xl mx-auto bg-gray-100 rounded-lg shadow-md p-6">
            <livewire:reports.reportcard :studentId="$selectedStudentId"/>
        </div>
    </div>
</x-app-layout>