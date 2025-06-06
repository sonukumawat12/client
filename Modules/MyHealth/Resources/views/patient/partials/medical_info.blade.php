<!-- Main content -->
<section class="content">
    <table class="table  table-striped" id="medical_info_table">

        <tr>
            <th>@lang('myhealth::patient.age')</th>
            <td>
                {{ $age }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.address')</th>
            <td>
                {{ $patient_details->address  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.country')</th>
            <td>
                {{ $patient_details->country  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.gender')</th>
            <td>
                {{ $gender  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.marital_status')</th>
            <td>
                {{ $marital_status  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.guardian_name')</th>
            <td>
                {{ $patient_details->guardian_name  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.mobile')</th>
            <td>
                {{ $patient_details->mobile  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.blood_group')</th>
            <td>
                {{ $blood_group }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.height')</th>
            <td>
                {{ $patient_details->height  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.weight')</th>
            <td>
                {{ $patient_details->weight  }}
            </td>
        </tr>
        <tr>
            <th>@lang('myhealth::patient.allergy')</th>
            <td>
                {{ $patient_details->known_allergies }}
            </td>
        </tr>
    </table> 

</section>
<!-- /.content -->