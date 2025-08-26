<!-- Role Selection -->
<div class="mt-4">
    <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
    <select id="role" name="role" required class="input-field">
        <option value="employee">Employee</option>
        <option value="manager">Manager</option>
    </select>
</div>

<!-- Manager Selection (shown only if role is employee) -->
<div id="manager_field" class="mt-4" style="display: none;">
    <label for="manager_id" class="block font-medium text-sm text-gray-700">Select Your Manager</label>
    <select id="manager_id" name="manager_id" class="input-field">
        <option value="">-- Select Manager --</option>
        @foreach($managers as $manager)
            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
        @endforeach
    </select>
</div>
<script>
    document.getElementById('role').addEventListener('change', function() {
        const managerField = document.getElementById('manager_field');
        managerField.style.display = this.value === 'employee' ? 'block' : 'none';
    });
</script>
