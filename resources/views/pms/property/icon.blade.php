@extends('pms.layouts.app')
@section('content')

<section class="section">
    <div class="container">

        {{-- ================= TITLE ================= --}}
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">
                        {{ $id ? 'Update' : 'Add' }} {{ ucfirst(request()->pType) }}
                        (Property: {{ $parentHome->unit_name ?? '' }})
                    </h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.property.unit.or.multiunit.list', [
                'property_id'=>request()->property_id,
                'pType'=>request()->pType
            ]) }}"
                        class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i>
                        <span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="row g-5">

                {{-- LEFT MENU --}}
                @include('pms.property.unit-or-multiunit-menu-segments')

                {{-- RIGHT CONTENT --}}
                <div class="col-12 col-lg-9">

                    {{-- TABS --}}
                    <ul class="nav nav-tabs mb-3" id="iconTabs">
                        <li class="nav-item">
                            <button class="nav-link {{ session('active_tab','house_rules') == 'house_rules' ? 'active' : '' }}" data-bs-toggle="tab"
                                data-bs-target="#house_rules" data-type="house_rules">
                                House Rules
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link {{ session('active_tab') == 'safety_property' ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#safety_property" data-type="safety_property">
                                Safety & Property
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link {{ session('active_tab') == 'cancellation_policy' ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#cancellation_policy" data-type="cancellation_policy">
                                Cancellation Policy
                            </button>
                        </li>
                    </ul>

                    <form method="POST"
                        action="{{ route('pms.property.unit.or.multiunit.saveHomeImportantInformation') }}">
                        @csrf

                        <input type="hidden" name="home_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="type" id="type_option"
                            value="{{ session('active_tab','house_rules') }}">
                        <div class="tab-content">

                            @php
                            $blocks = [
                            'house_rules' => $houseRules,
                            'safety_property' => $safety,
                            'cancellation_policy' => $cancellation
                            ];
                            @endphp

                            @foreach($blocks as $type => $rows)
                            <div class="tab-pane fade {{ session('active_tab','house_rules') === $type ? 'show active' : '' }}"
                                id="{{ $type }}">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Icon</th>
                                            <th>Title</th>
                                            <th>Sub Title</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody class="icon-body" data-type="{{ $type }}">
                                        @foreach($rows as $i => $row)
                                        <tr>
                                            <td>
                                                <select name="important_information[{{ $type }}][{{ $i }}][icon_id]" class="form-select">
                                                    <option value="">Select Icon</option>
                                                    @foreach($icons as $icon)
                                                    <option value="{{ $icon->id }}"
                                                        {{ (string)$row->icon_id === (string)$icon->id ? 'selected' : '' }}>
                                                        {{ $icon->icons_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden"
                                                    name="important_information[{{ $type }}][{{ $i }}][id]"
                                                    value="{{ $row->id }}">
                                            </td>

                                            <td>
                                                <input type="text"
                                                    name="important_information[{{ $type }}][{{ $i }}][title]"
                                                    value="{{ $row->title }}"
                                                    class="form-control">
                                            </td>

                                            <td>
                                                <input type="text"
                                                    name="important_information[{{ $type }}][{{ $i }}][sub_title]"
                                                    value="{{ $row->sub_title }}"
                                                    class="form-control">
                                            </td>

                                            <td>
                                                <button type="button"
                                                    class="btn p-1 fs-5 text-black removeRow">
                                                    <i class="icon-bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <button type="button"
                                    class="btn btn-save btn-secondary btn-small addRow"
                                    data-type="{{ $type }}">
                                    <i class="bi bi-plus-lg me-2"></i>
                                    Add More
                                </button>

                            </div>
                            @endforeach

                        </div>

                        <!-- <button type="submit" class="btn btn-save btn-primary mt-3">
                            <span>SUBMIT</span>
                        </button> -->
                        <button type="submit" id="submitBtn" class="btn btn-save btn-primary mt-3">
                                    <span id="btnText">SUBMIT</span>
                                    <span id="btnLoader"
                                        class="spinner-border spinner-border-sm ms-2 d-none"
                                        role="status"
                                        aria-hidden="true"></span>
                                </button>


                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const activeTab = "{{ session('active_tab','house_rules') }}";
        document.getElementById('type_option').value = activeTab;

        document.querySelectorAll('#iconTabs button').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('type_option').value = btn.dataset.type;
            });
        });

        document.addEventListener('click', function(e) {

            /* ADD ROW */
            const addBtn = e.target.closest('.addRow');
            if (addBtn) {
                const type = addBtn.dataset.type;
                const tbody = document.querySelector(`tbody[data-type="${type}"]`);
                const index = tbody.children.length;

                tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>
                        <select name="important_information[${type}][${index}][icon_id]" class="form-select">
                            <option value="">Select Icon</option>
                            @foreach($icons as $icon)
                                <option value="{{ $icon->id }}">{{ $icon->icons_name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden"
                               name="important_information[${type}][${index}][id]"
                               value="">
                    </td>

                    <td>
                        <input type="text"
                               name="important_information[${type}][${index}][title]"
                               class="form-control">
                    </td>

                    <td>
                        <input type="text"
                               name="important_information[${type}][${index}][sub_title]"
                               class="form-control">
                    </td>

                    <td>
                        <button type="button"
                                class="btn p-1 fs-5 text-black removeRow">
                            <i class="icon-bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            }

            /* REMOVE ROW */
            if (e.target.closest('.removeRow')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>


@endsection