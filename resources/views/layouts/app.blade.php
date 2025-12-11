<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <!--begin::Head-->

    <head>
        <title>{{ $pageTitle ? $pageTitle . ' - ' . config('app.name', 'New HRM') : config('app.name', 'New HRM') }}
        </title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- <link rel="shortcut icon" href="{{ asset('media/logos/favicon.ico') }}" /> -->
        <link rel="shortcut icon" href="{{ asset('media/logos/heuer-logo-red-min.png') }}" />

        <!--begin::Fonts-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
        <!--end::Fonts-->

        <!--begin::Page Vendor Stylesheets(used by this page)-->
        <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('plugins/custom/datatables/fixedColumns.dataTables.min.css') }}" rel="stylesheet"
            type="text/css" />

        <link href="{{ asset('plugins/custom/datatables/select.dataTables.css') }}" rel="stylesheet" type="text/css" />

        <link href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css" rel="stylesheet" type="text/css" />

        <!--begin::Global Stylesheets Bundle(used by all pages)-->
        <link href="{{ asset('plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css" />

        <link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
        <!--end::Global Stylesheets Bundle-->

        <!-- Scripts -->
        <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    </head>
    <!--end::Head-->
    <!--begin::Body-->

    <body id="kt_body"
        class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
        style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
        <!--begin::Main-->
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Page-->
            <div class="page d-flex flex-row flex-column-fluid">
                <!--begin::Aside-->
                @include('layouts.partials.aside')
                <!--end::Aside-->
                <!--begin::Wrapper-->
                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                    <!--begin::Header-->
                    @include('layouts.partials.header')
                    <!--end::Header-->
                    <!--begin::Content-->
                    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">


                        {{-- <div class="container">
                            <div
                                class="alert bg-light-primary border border-primary border-3 d-flex flex-column flex-sm-row w-100 p-5 mb-10">
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h5 class="mb-1 text-primary">Notice</h5>
                                    <span class="text-primary">We’re currently experiencing a temporary issue with the
                                        biometric system. As a result, you may notice some unexpected absentee records
                                        for 05 May 2025.<br>
                                        No need to worry — this will be resolved by tomorrow, and attendance data will
                                        be updated accordingly.<br>
                                        <strong>In the meantime, please don’t forget to punch in and out as
                                            usual.<strong></span>
                                </div>
                            </div>
                        </div> --}}

                        {{ $slot }}




                    </div>
                    <!--end::Content-->
                    <!--begin::Footer-->
                    @include('layouts.partials.footer')
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::Root-->
        <!--end::Main-->

        <!--begin::Scrolltop-->
        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
            <span class="svg-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none">
                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1"
                        transform="rotate(90 13 6)" fill="black" />
                    <path
                        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                        fill="black" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Scrolltop-->
        <!--begin::Modals-->




        <!--end::Modals-->
        <!--begin::Javascript-->
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('js/scripts.bundle.js') }}"></script>
        <!--end::Global Javascript Bundle-->
        <!--begin::Page Vendors Javascript(used by this page)-->
        <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script src="{{ asset('plugins/custom/datatables/dataTables.fixedColumns.min.js') }}"></script>
        <!--end::Page Vendors Javascript-->
        <!--begin::Page Custom Javascript(used by this page)-->
        <script src="{{ asset('js/custom/apps/file-manager/list.js') }}"></script>
        <script src="{{ asset('js/widgets.bundle.js') }}"></script>
        <script src="{{ asset('js/custom/widgets.js') }}"></script>
        <script src="{{ asset('js/custom/apps/chat/chat.js') }}"></script>
        <script src="{{ asset('js/custom/utilities/modals/upgrade-plan.js') }}"></script>
        <script src="{{ asset('js/custom/utilities/modals/create-app.js') }}"></script>
        <script src="{{ asset('js/custom/utilities/modals/users-search.js') }}"></script>
        <!--end::Page Custom Javascript-->

        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

        {{ $scripts ?? '' }}
        <!--end::Javascript-->
    </body>
    <!--end::Body-->

</html>
