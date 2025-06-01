<div class="card-body">
    <div class="form-group">
        <label for="loan_charge_id" class="control-label">{{ trans_choice('loan::general.fee', 1) }}</label>
        <select class="form-control  @error('loan_charge_id') is-invalid @enderror" name="loan_charge_id" id="loan_charge_id"
            v-model="loan_charge_id">
            <option value=""></option>
            <option v-for="charge in charges" :value="charge.id">@{{ charge.name }}</option>
        </select>
        @error('loan_charge_id')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="charge_amount" class="control-label">{{ trans_choice('core.fee', 1) }}
            {{ trans('core.amount') }}</label>
        <input type="text" name="charge_amount" value="{{ old('charge_amount') }}" v-model="charge_amount"
            class="form-control @error('charge_amount') is-invalid @enderror numeric" :readonly="!allow_override">
        @error('charge_amount')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="charge_date" class="control-label">{{ trans_choice('core.fee', 1) }}
            {{ trans('core.date') }}</label>
        <input type="text" v-model="charge_date" class="form-control datepicker @error('charge_date') is-invalid @enderror"
            name="charge_date" id="charge_date" />

        @error('charge_date')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
