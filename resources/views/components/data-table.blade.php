@props([
    'headers' => [],
    'data' => [],
    'searchable' => true,
    'sortable' => true,
    'pagination' => null,
    'emptyMessage' => 'No data available',
    'actions' => false,
    'striped' => true,
    'hover' => true,
    'responsive' => true
])

@php
$tableClasses = [
    'table',
    'table-striped' => $striped,
    'table-hover' => $hover
];

$tableId = 'datatable-' . uniqid();
@endphp

<div class="card">
    @if($searchable)
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="card-title mb-0">{{ $title ?? 'Data Table' }}</h6>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i data-lucide="search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search..." id="{{ $tableId }}-search">
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card-body {{ $responsive ? 'p-0' : '' }}">
        <div class="{{ $responsive ? 'table-responsive' : '' }}">
            <table class="{{ implode(' ', array_filter($tableClasses)) }}" id="{{ $tableId }}">
                <thead>
                    <tr>
                        @foreach($headers as $key => $header)
                            <th
                                @if($sortable)
                                    class="sortable cursor-pointer"
                                    data-sort="{{ is_string($key) ? $key : $loop->index }}"
                                @endif
                            >
                                {{ is_array($header) ? $header['label'] : $header }}
                                @if($sortable)
                                    <i data-lucide="chevrons-up-down" class="sort-icon ms-1"></i>
                                @endif
                            </th>
                        @endforeach
                        @if($actions)
                            <th width="120">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            @foreach($headers as $key => $header)
                                @php
                                    $field = is_string($key) ? $key : (is_array($header) ? $header['field'] : $loop->index);
                                    $value = is_object($row) ? $row->$field : $row[$field] ?? '';
                                @endphp
                                <td>
                                    @if(is_array($header) && isset($header['render']))
                                        {!! $header['render']($value, $row) !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach
                            @if($actions)
                                <td>
                                    @if(isset($actionSlot))
                                        {{ $actionSlot($row) }}
                                    @else
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i data-lucide="eye"></i>
                                            </button>
                                            <button class="btn btn-outline-warning" title="Edit">
                                                <i data-lucide="edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Delete">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="text-center py-4">
                                <div class="text-muted">
                                    <i data-lucide="inbox" class="mb-2"></i>
                                    <p>{{ $emptyMessage }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pagination)
        <div class="card-footer">
            {{ $pagination->links() }}
        </div>
    @endif
</div>

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Simple table search functionality
            document.querySelectorAll('[id$="-search"]').forEach(searchInput => {
                const tableId = searchInput.id.replace('-search', '');
                const table = document.getElementById(tableId);

                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tbody = table.querySelector('tbody');
                    const rows = tbody.querySelectorAll('tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            });

            // Simple table sorting functionality
            document.querySelectorAll('.sortable').forEach(header => {
                header.addEventListener('click', function() {
                    const table = this.closest('table');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const index = Array.from(this.parentNode.children).indexOf(this);
                    const isAsc = this.classList.contains('sort-asc');

                    // Reset all headers
                    table.querySelectorAll('.sortable').forEach(h => {
                        h.classList.remove('sort-asc', 'sort-desc');
                    });

                    // Sort rows
                    rows.sort((a, b) => {
                        const aText = a.children[index].textContent.trim();
                        const bText = b.children[index].textContent.trim();

                        if (isAsc) {
                            return bText.localeCompare(aText);
                        } else {
                            return aText.localeCompare(bText);
                        }
                    });

                    // Update header class
                    this.classList.add(isAsc ? 'sort-desc' : 'sort-asc');

                    // Append sorted rows
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    </script>
    @endpush
@endonce