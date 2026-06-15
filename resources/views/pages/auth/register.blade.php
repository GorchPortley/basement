<x-layouts::auth :title="__('Register')">
    <div class="min-h-screen flex items-center justify-center" >
    <x-card class="border-2 border-accent" title="Register">
        <x-form action="/register" method="POST">
                @csrf
                <x-input label="Username" name="name" :value="old('name')" required autofocus />
                <x-input label="Email" type="email" name="email" :value="old('email')" required />
                <x-password label="Password" name="password" required />
                <x-password label="Confirm Password" name="password_confirmation" required />
                <x-slot:actions class="block w-full">
                <x-button type="submit" class="w-full btn-primary" label="Register" />
                </x-slot:actions>
        </x-form>
    </x-card>
</div>
</x-layouts::auth>
