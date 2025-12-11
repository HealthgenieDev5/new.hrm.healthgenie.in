<x-app-layout>

	<x-slot name="pageTitle">
		{{ $pageTitle }}
	</x-slot>

	<x-flash-message />

	<div class="post d-flex flex-column-fluid" id="kt_post">

		<div id="kt_content_container" class="container-xxl">

			<h2>Edit Retention Group</h2>
			<form method="POST" action="{{ route('retention-groups.update', $group->id) }}">
				@csrf
				@method('PUT')
				<div class="mb-3">
					<label>Label</label>
					<input type="text" name="label" class="form-control" value="{{ old('label', $group->label) }}" required>
				</div>
				<div class="mb-3">
					<label>Min Days</label>
					<input type="number" name="min" class="form-control" value="{{ old('min', $group->min) }}" required>
				</div>
				<div class="mb-3">
					<label>Max Days</label>
					<input type="number" name="max" class="form-control" value="{{ old('max', $group->max) }}" required>
				</div>
				<div class="mb-3">
					<label>Points</label>
					<input type="number" name="points" class="form-control" value="{{ old('points', $group->points) }}" required>
				</div>
				<button type="submit" class="btn btn-success">Update</button>
				<a href="{{ route('retention-groups.index') }}" class="btn btn-secondary">Cancel</a>
			</form>

		</div>

	</div>

</x-app-layout>