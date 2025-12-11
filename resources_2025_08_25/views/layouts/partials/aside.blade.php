<!--begin::Aside-->
<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="/" class="flex-grow-1">
            <img alt="Logo" src="{{ asset('media/logos/heuer-logo-red-min.png') }}" class="h-50px logo mx-auto" />
        </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none">
                    <path opacity="0.5"
                        d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z"
                        fill="black" />
                    <path
                        d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z"
                        fill="black" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside toggler-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid">
        <!--begin::Aside Menu-->
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
            data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">





                <div class="menu-item {{ request()->routeIs('dashboard.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="bi bi-grid fs-3"></i>
                            </span>
                        </span>
                        <span class="menu-title">Dashboards</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">

                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                                href="{{ route('dashboard.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('dashboard.retention') ? 'active' : '' }}"
                                href="{{ route('dashboard.retention') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Retention Report</span>
                            </a>
                        </div>

                    </div>
                </div>

                @hasPermissionTo(['View Attendance'])
                <div class="menu-item {{ request()->routeIs('attendance.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="bi bi-arrow-down-up fs-3"></i>
                            </span>
                        </span>
                        <span class="menu-title">Attendance</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">

                        @hasPermissionTo(['View Attendance'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('attendance.summary') ? 'active' : '' }}"
                                href="{{ route('attendance.summary') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Monthly Attendance</span>
                            </a>
                        </div>
                        @endhasPermissionTo


                        @hasPermissionTo(['View Attendance'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}"
                                href="{{ route('attendance.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Time Attendance</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo(['View Attendance'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('attendance.missed-punch') ? 'active' : '' }}"
                                href="{{ route('attendance.missed-punch') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Missed Punch dashboard</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo(['View Attendance'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('attendance.wrong-shift') ? 'active' : '' }}"
                                href="{{ route('attendance.wrong-shift') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Wrong Shifts</span>
                            </a>
                        </div>
                        @endhasPermissionTo


                        @hasPermissionTo(['View Attendance'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('attendance.continuous-absent') ? 'active' : '' }}"
                                href="{{ route('attendance.continuous-absent') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Continuous Absent</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo(['Recalculate Attendance'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('attendance.recalculate') ? 'active' : '' }}"
                                href="{{ route('attendance.recalculate') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Recalculate</span>
                            </a>
                        </div>
                        @endhasPermissionTo



                    </div>

                </div>
                @endhasPermissionTo


                @hasPermissionTo(['Manage Planning'])
                <div class="menu-item {{ request()->routeIs('production.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                {{-- <i class="bi bi-arrow-down-up fs-3"></i> --}}
                                <i class="fonticon-drone fs-2x"></i>
                            </span>
                        </span>
                        <span class="menu-title">Production</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">

                        @hasPermissionTo(['Manage Planning'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('production.planning.index') ? 'active' : '' }}"
                                href="{{ route('production.planning.index') }}">
                                <span class="menu-bullet">

                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Planning</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('production.temporary-line-assignment.index') ? 'active' : '' }}"
                                href="{{ route('production.temporary-line-assignment.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Temporary Assignments</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                    </div>

                </div>
                @endhasPermissionTo


                @hasPermissionTo([
                'Manage Psp',
                'Manage Holidays',
                'Manage Divisions',
                'Manage Departments',
                'Manage Sub Departments',
                'Manage Lines',
                'Manage Processes',
                'Manage Levels',
                'Manage Designations',
                'Manage Workertypes',
                'Manage Salary Master',
                'Manage Working Area'
                ])
                <div class="menu-item {{ request()->routeIs('master.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="bi bi-briefcase fs-3"></i>
                            </span>
                        </span>
                        <span class="menu-title">Master</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">

                        @hasPermissionTo('Manage PSP')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.psps.*') ? 'active' : '' }}"
                                href="{{ route('master.psps.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Psps</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Holidays')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.holidays.*') ? 'active' : '' }}"
                                href="{{ route('master.holidays.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Holidays</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Divisions')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.divisions.*') ? 'active' : '' }}"
                                href="{{ route('master.divisions.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Divisions</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Departments')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.departments.*') ? 'active' : '' }}"
                                href="{{ route('master.departments.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Departments</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Sub Departments')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.subdepartments.*') ? 'active' : '' }}"
                                href="{{ route('master.subdepartments.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Sub Departments</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Lines')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.lines.*') ? 'active' : '' }}"
                                href="{{ route('master.lines.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Lines</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Processes')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.processes.*') ? 'active' : '' }}"
                                href="{{ route('master.processes.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Processes</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Levels')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.levels.*') ? 'active' : '' }}"
                                href="{{ route('master.levels.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Levels</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Designations')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.designations.*') ? 'active' : '' }}"
                                href="{{ route('master.designations.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Designations</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Workertypes')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.workertypes.*') ? 'active' : '' }}"
                                href="{{ route('master.workertypes.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Worker Types</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Shifts')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.shifts.*') ? 'active' : '' }}"
                                href="{{ route('master.shifts.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Shifts</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Salary Master')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.salary-master.*') ? 'active' : '' }}"
                                href="{{ route('master.salary-master.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Salary Master</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Working Area')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.working-area.*') ? 'active' : '' }}"
                                href="{{ route('master.working-area.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Working Area</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage Production Product')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('master.production-product.*') ? 'active' : '' }}"
                                href="{{ route('master.production-product.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Production Product</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                    </div>

                </div>
                @endhasPermissionTo


                @hasPermissionTo(['Create worker', 'Edit worker', 'View workers', 'Delete worker', 'Edit Worker Long Form'])
                <div class="menu-item {{ request()->routeIs('workers.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="bi bi-people fs-3"></i>
                            </span>
                        </span>
                        <span class="menu-title">Workers</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @hasPermissionTo(['View workers'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('workers.index') ? 'active' : '' }}"
                                href="{{ route('workers.index', ['employment_status' => 'Active']) }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">All Workers</span>
                            </a>
                        </div>
                        @endhasPermissionTo
                        @hasPermissionTo(['Create worker'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('workers.create') ? 'active' : '' }}"
                                href="{{ route('workers.create') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Add New Worker</span>
                            </a>
                        </div>
                        @endhasPermissionTo
                    </div>

                </div>
                @endhasPermissionTo


                @hasPermissionTo(['Manage Shift Override', 'Manual Punching'])
                <div class="menu-item {{ request()->routeIs('overrides.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M18.4 18H16C15.7 18 15.5 17.9 15.3 17.7L12.5 14.9C12.1 14.5 12.1 13.9 12.5 13.5C12.9 13.1 13.5 13.1 13.9 13.5L16.4 16H18.4V18ZM16 6C15.7 6 15.5 6.09999 15.3 6.29999L11 10.6L6.70001 6.29999C6.50001 6.09999 6.3 6 6 6H3C2.4 6 2 6.4 2 7C2 7.6 2.4 8 3 8H5.60001L9.60001 12L5.60001 16H3C2.4 16 2 16.4 2 17C2 17.6 2.4 18 3 18H6C6.3 18 6.50001 17.9 6.70001 17.7L16.4 8H18.4V6H16Z"
                                        fill="black" />
                                    <path opacity="0.3"
                                        d="M21.7 6.29999C22.1 6.69999 22.1 7.30001 21.7 7.70001L18.4 11V3L21.7 6.29999ZM18.4 13V21L21.7 17.7C22.1 17.3 22.1 16.7 21.7 16.3L18.4 13Z"
                                        fill="black" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Overrides</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @hasPermissionTo('Manage Shift Override')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('overrides.shiftoverride.*') ? 'active' : '' }}"
                                href="{{ route('overrides.shiftoverride.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Shift Overrides</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manual Punching')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('overrides.manualpunching.*') ? 'active' : '' }}"
                                href="{{ route('overrides.manualpunching.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Manual Punching</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                    </div>

                </div>
                @endhasPermissionTo

                @hasPermissionTo(['Download Attendance', 'View Salary Report'])
                <div class="menu-item {{ request()->routeIs('reports.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z"
                                        fill="black" />
                                    <path
                                        d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z"
                                        fill="black" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Reports</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        @hasPermissionTo(['Download Attendance'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('reports.attendance-report.index') ? 'active' : '' }}"
                                href="{{ route('reports.attendance-report.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Download Attendance</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo(['View Salary Report'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('reports.salary.index') ? 'active' : '' }}"
                                href="{{ route('reports.salary.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Salary Report</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo(['View Salary Report'])
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('reports.salary.incentives') ? 'active' : '' }}"
                                href="{{ route('reports.salary.incentives') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Incentive Report</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                    </div>

                </div>
                @endhasPermissionTo

                @hasPermissionTo(['Manage App Settings'])
                <div class="menu-item {{ request()->routeIs('app-settings.*') ? 'here show' : '' }} menu-accordion"
                    data-kt-menu-trigger="click">

                    <span class="menu-link">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3"
                                        d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z"
                                        fill="black" />
                                    <path
                                        d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z"
                                        fill="black" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">App Settings</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion menu-active-bg">

                        @hasPermissionTo('Manage user roles')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('app-settings.users.*') ? 'active' : '' }}"
                                href="{{ route('app-settings.users.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">User Role Mapping</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage roles')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('app-settings.roles.*') ? 'active' : '' }}"
                                href="{{ route('app-settings.roles.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Role Permission Mapping</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                        @hasPermissionTo('Manage permissions')
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('app-settings.permissions.*') ? 'active' : '' }}"
                                href="{{ route('app-settings.permissions.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Permissions</span>
                            </a>
                        </div>
                        @endhasPermissionTo

                    </div>

                </div>
                @endhasPermissionTo

                {{--
                @hasPermissionTo('Import Workers')
                <div class="menu-item {{ request()->routeIs('import.*') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('import.workers') }}">
                    <span class="menu-icon">
                        <i class="bi bi-capslock fs-3"></i>
                    </span>
                    <span class="menu-title">Import Workers</span>
                </a>
            </div>
            @endhasPermissionTo
            --}}

        </div>
        <!--end::Menu-->
    </div>
    <!--end::Aside Menu-->
</div>
<!--end::Aside menu-->
<!--begin::Footer-->
<!-- <div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
        <a href="./" class="btn btn-custom btn-primary w-100" data-bs-toggle="tooltip" data-bs-trigger="hover"
            data-bs-dismiss-="click" title="200+ in-house components and 3rd-party plugins">
            <span class="btn-label">Docs &amp; Components</span>
            <span class="svg-icon btn-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3"
                        d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM12.5 18C12.5 17.4 12.6 17.5 12 17.5H8.5C7.9 17.5 8 17.4 8 18C8 18.6 7.9 18.5 8.5 18.5L12 18C12.6 18 12.5 18.6 12.5 18ZM16.5 13C16.5 12.4 16.6 12.5 16 12.5H8.5C7.9 12.5 8 12.4 8 13C8 13.6 7.9 13.5 8.5 13.5H15.5C16.1 13.5 16.5 13.6 16.5 13ZM12.5 8C12.5 7.4 12.6 7.5 12 7.5H8C7.4 7.5 7.5 7.4 7.5 8C7.5 8.6 7.4 8.5 8 8.5H12C12.6 8.5 12.5 8.6 12.5 8Z"
                        fill="black" />
                    <rect x="7" y="17" width="6" height="2" rx="1" fill="black" />
                    <rect x="7" y="12" width="10" height="2" rx="1" fill="black" />
                    <rect x="7" y="7" width="6" height="2" rx="1" fill="black" />
                    <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black" />
                </svg>
            </span>
        </a>
    </div> -->
<!--end::Footer-->
</div>
<!--end::Aside-->