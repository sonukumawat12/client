@extends('layouts.app')
@section('title')
    {{ $loan->loan_product->name }}(#{{ $loan->id }})
@endsection

@section('content')

    
    <!-- Main content -->
    <section class="content no-print" id="vue-app">

        @can('product.view')
            <div class="row">

                @component('components.widget')
                    @slot('slot')
                        <section class="content">
                            <form method="post" action="{{ url('contact_loan/' . $loan->id . '/update') }}">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">
                                        <h3 class="alert bg-gray">{{ trans_choice('core.detail', 2) }}</h3>
                                        @include('loan::loan.partials.details')

                                        <h3 class="alert bg-gray">{{ trans_choice('loan::general.term', 2) }}</h3>
                                        @include('loan::loan.partials.terms')

                                        <h3 class="alert bg-gray">{{ trans_choice('core.setting', 2) }}</h3>
                                        @include('loan::loan.partials.settings')

                                        <h3 class="alert bg-gray">{{ trans_choice('core.accounting', 2) }}</h3>
                                        @include('loan::loan.partials.accounting')
                                    </div>
                                    <div class="card-footer border-top ">
                                        <button type="submit" class="btn btn-primary float-right">{{ trans_choice('core.save', 1) }}</button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    @endslot
                @endcomponent

            </div>
        @endcan

    </section>

@stop
@section('javascript')
    {{-- Scroll to the dropdown where the user selects the loan officers who approve the loan --}}
    @if (Request::get('action') == 'change_approval_officers')
        <script>
            $(document).ready(function() {
                $('html, body').animate({
                    scrollTop: $("#change_approval_officers_div").offset().top
                }, 1000);
            });
        </script>
    @endif
    <script src="{{ asset('modules/accounting/js/helper-functions.js') }}"></script>
    <script>
        var app = new Vue({
            el: '#vue-app',
            data: {
                contact_type: "{{ old('contact_type', $loan->contact_type) }}",
                loan_product_id: parseInt("{{ old('loan_product_id', $loan->loan_product_id) }}"),
                location_id: parseInt("{{ old('location_id', $loan->location_id) }}"),
                contact_id: parseInt("{{ old('contact_id', $loan->contact_id) }}"),
                group_id: parseInt("{{ old('group_id', $loan->group_id) }}"),
                external_id: "{{ old('external_id', $loan->external_id) }}",
                variation_id: "{{ old('variation_id', $loan->variation_id) }}",
                applied_amount: "{{ old('applied_amount', $loan->applied_amount) }}",
                loan_term: "{{ old('loan_term', $loan->loan_term) }}",
                repayment_frequency: "{{ old('repayment_frequency', $loan->repayment_frequency) }}",
                repayment_frequency_type: "{{ old('repayment_frequency_type', $loan->repayment_frequency_type) }}",
                interest_rate: "{{ old('interest_rate', $loan->interest_rate) }}",
                expected_disbursement_date: "{{ old('expected_disbursement_date', $loan->expected_disbursement_date) }}",
                loan_officer_id: parseInt("{{ old('loan_officer_id', $loan->loan_officer_id) }}"),
                expected_first_payment_date: "{{ old('expected_first_payment_date', $loan->expected_first_payment_date) }}",
                loan_purpose_id: parseInt("{{ old('loan_purpose_id', $loan->loan_purpose_id) }}"),
                loan_products: {!! json_encode($loan_products) !!},
                contacts: {!! json_encode($contacts) !!},
                loan_purposes: {!! json_encode($loan_purposes) !!},
                business_locations: {!! json_encode($business_locations) !!},
                users: {!! json_encode($users) !!},
                client_types: {!! json_encode($client_types) !!},
                repayment_frequency_types: {!! json_encode($repayment_frequency_types) !!},

                amortization_methods: {!! json_encode($amortization_methods) !!},
                loan_transaction_processing_strategies: {!! json_encode($loan_transaction_processing_strategies) !!},
                grace_on_principal_paid: "{{ old('grace_on_principal_paid', $loan->grace_on_principal_paid) }}",
                grace_on_interest_paid: "{{ old('grace_on_interest_paid', $loan->grace_on_interest_paid) }}",
                grace_on_interest_charged: "{{ old('grace_on_interest_charged', $loan->grace_on_interest_charged) }}",
                interest_methodology: "{{ old('interest_methodology', $loan->interest_methodology) }}",
                amortization_method: "{{ old('amortization_method', $loan->amortization_method) }}",
                loan_transaction_processing_strategy_id: parseInt(
                    "{{ old('loan_transaction_processing_strategy_id', $loan->loan_transaction_processing_strategy_id) }}"),
                loan_approval_officers: {!! old('loan_approval_officers')
                    ? json_encode(explode(',', old('loan_approval_officers')), JSON_NUMERIC_CHECK)
                    : json_encode($loan->approval_officers->pluck('id')) !!},
                interest_rate_type: "{{ old('interest_rate_type', $loan->interest_rate_type) }}",
                currency: {!! json_encode($currency) !!},
                variations: {!! json_encode($variations) !!},

                /**Accounting start**/
                assets: {!! json_encode($assets) !!},
                liabilities: {!! json_encode($liabilities) !!},
                income: {!! json_encode($income) !!},
                expenses: {!! json_encode($expenses) !!},

                accounting_rule: "{{ old('accounting_rule', $loan->accounting_rule)  }}",

                //asset
                fund_source_chart_of_account_id: parseInt("{{ old('fund_source_chart_of_account_id', $loan->fund_source_chart_of_account_id)  }}"),
                loan_portfolio_chart_of_account_id: parseInt("{{ old('loan_portfolio_chart_of_account_id', $loan->loan_portfolio_chart_of_account_id)  }}"),
                suspended_income_chart_of_account_id: parseInt("{{ old('suspended_income_chart_of_account_id', $loan->suspended_income_chart_of_account_id)  }}"),
                interest_receivable_chart_of_account_id: parseInt("{{ old('interest_receivable_chart_of_account_id', $loan->interest_receivable_chart_of_account_id)  }}"),
                fees_receivable_chart_of_account_id: parseInt("{{ old('fees_receivable_chart_of_account_id', $loan->fees_receivable_chart_of_account_id)  }}"),
                penalties_receivable_chart_of_account_id: parseInt("{{ old('penalties_receivable_chart_of_account_id', $loan->penalties_receivable_chart_of_account_id)  }}"),
                transfer_in_suspense_chart_of_account_id: parseInt("{{ old('transfer_in_suspense_chart_of_account_id', $loan->transfer_in_suspense_chart_of_account_id)  }}"),

                //income
                income_from_interest_chart_of_account_id: parseInt("{{ old('income_from_interest_chart_of_account_id', $loan->income_from_interest_chart_of_account_id)  }}"),
                income_from_penalties_chart_of_account_id: parseInt("{{ old('income_from_penalties_chart_of_account_id', $loan->income_from_penalties_chart_of_account_id)  }}"),
                income_from_fees_chart_of_account_id: parseInt("{{ old('income_from_fees_chart_of_account_id', $loan->income_from_fees_chart_of_account_id)  }}"),
                income_from_recovery_chart_of_account_id: parseInt("{{ old('income_from_recovery_chart_of_account_id', $loan->income_from_recovery_chart_of_account_id)  }}"),

                //expenses
                losses_written_off_chart_of_account_id: parseInt("{{ old('losses_written_off_chart_of_account_id', $loan->losses_written_off_chart_of_account_id)  }}"),
                interest_written_off_chart_of_account_id: parseInt("{{ old('interest_written_off_chart_of_account_id', $loan->interest_written_off_chart_of_account_id)  }}"),

                //liabilities
                overpayments_chart_of_account_id: parseInt("{{ old('overpayments_chart_of_account_id', $loan->overpayments_chart_of_account_id)  }}"),

                auto_disburse: "{{ old('auto_disburse', $loan->auto_disburse)  }}",
                /**Accounting end**/
            },

            watch: {
                contact_type() {
                    this.contact_id = '';
                },

                location_id() {
                    this.loan_product_id = '';
                }
            },

            computed: {
                computed_contacts() {
                    return this.contacts.filter((contact) => {
                        return this.contact_type == contact.type;
                    });
                },

                computed_loan_products() {
                    return this.loan_products.filter(product => {
                        return product.product_locations ?
                            product.product_locations.filter(product_location => product_location.id == this.location_id)
                            .length :
                            false;
                    });
                },

                is_contact_type_chosen() {
                    return this.contact_type;
                },

                is_location_chosen() {
                    return this.location_id;
                },

                business_location() {
                    const location = this.business_locations.find(location => location.id == this.location_id);
                    return location ?? {
                        name: ''
                    };
                },

                contact() {
                    const contact = this.computed_contacts.find(contact => contact.id == this.contact_id);
                    return contact ?? {
                        name: ''
                    };
                },

                loan_product() {
                    const product = this.loan_products.find(product => product.id == this.loan_product_id);
                    return product ?? {
                        name: ''
                    };
                },

                loan_officer() {
                    const loan_officer = this.users.find(user => user.id == this.loan_officer_id);
                    return loan_officer ?? {
                        user_full_name: ''
                    };
                },

                loan_purpose() {
                    const loan_purpose = this.loan_purposes.find(loan_purpose => loan_purpose.id == this.loan_purpose_id);
                    return loan_purpose ?? {
                        name: ''
                    };
                },

                loan_term_in_days() {
                    // function in js/helper-functions.js
                    return calculate_toan_term_in_days(this.loan_term, this.repayment_frequency_type);
                },

                product() {
                    return this.loan_products.find(product => product.id == this.loan_product_id) ?? {
                        variations: null
                    };
                },

                loan_approval_officers_chosen() {
                    let officers = [];

                    if (this.loan_approval_officers.length > 0) {
                        for (const officer_id of this.loan_approval_officers) {
                            const officer = this.users.find(user => user.id == officer_id);
                            if (officer) {
                                officers.push(officer.user_full_name);
                            }
                        }
                    }
                    return officers.join(', ');
                },

                loan_transaction_processing_strategy() {
                    const ltp = this.loan_transaction_processing_strategies.find(ltp => ltp.id == this
                        .loan_transaction_processing_strategy_id);
                    return ltp ?? {
                        name: ''
                    };
                },

                amortization_method_chosen() {
                    return this.amortization_method != '' ? this.amortization_methods[this.amortization_method] : '';
                },

                variation() {
                    return this.variations.find(variation => variation.id == this.variation_id);
                }
            },

            methods: {
                loan_term_label(loan_term, repayment_frequency_type = null) {
                    if (!loan_term) {
                        return;
                    }
                    if (!repayment_frequency_type) {
                        repayment_frequency_type = this.repayment_frequency_type;
                    }
                    return loan_term > 1 ? repayment_frequency_type : repayment_frequency_type.slice(0, -1);
                },
            }
        });
    </script>
@endsection
