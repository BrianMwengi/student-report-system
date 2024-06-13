<x-app-layout>
    <div class="container mx-auto mt-5 px-4 lg:px-8">
        <div class="flex flex-wrap -mx-2">
            <!-- Student Create Component -->
            <div class="w-full lg:w-1/2 px-2">
                <livewire:students.create />
            </div>

            <!-- Student List Component -->
            <div class="w-full lg:w-1/2 px-2">
                <livewire:students.list />
            </div>
        </div>
    </div>
</x-app-layout>
