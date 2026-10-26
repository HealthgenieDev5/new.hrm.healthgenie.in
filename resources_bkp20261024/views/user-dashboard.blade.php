<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">

            <h3>No Data for you at this moment</h3>


        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {

            })
        </script>
    </x-slot>

</x-app-layout>
