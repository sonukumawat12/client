<?php

namespace Modules\Shipping\Entities;

use App\Contact;
use App\Transaction;
use App\BusinessLocation;
use Modules\Shipping\Entities\Driver;
use Illuminate\Database\Eloquent\Model;
use Modules\Shipping\Entities\ShippingMode;
use Modules\Shipping\Entities\ShippingAgent;
use Modules\Shipping\Entities\ShipmentPackage;
use Modules\Shipping\Entities\ShippingPackage;
use Modules\Shipping\Entities\ShippingPartner;
use Modules\Shipping\Entities\ShippingRecipient;

class Shipment extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = 'shipments';
     /**
     * Get the transection associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transection()
    {
        return $this->hasOne(Transaction::class, 'id' , 'transaction_id');
    }

    /**
     * Get all of the lineitem for the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lineitem()
    {
        return $this->hasMany(ShipmentPackage::class,'shipment_id');
    }

    /**
     * Get the location associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function location()
    {
        return $this->hasOne(BusinessLocation::class,'id', 'location_id' );
    }
    /**
     * Get the agent associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function agent()
    {
        return $this->hasOne(ShippingAgent::class, 'id','agent_id');
    }

    /**
     * Get the driver associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function driver()
    {
        return $this->hasOne(Driver::class, 'id','driver_id');
    }

    /**
     * Get the sender/customer associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender()
    {
        return $this->hasOne(Contact::class, 'id' , 'customer_id');
    }
    /**
     * Get the receiver associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function receiver()
    {
        return $this->hasOne(ShippingRecipient::class,'id' ,'recipient_id');
    }

    /**
     * Get the partner associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partner()
    {
        return $this->hasOne(ShippingPartner::class, 'id' , 'shipping_partner');
    }

    /**
     * Get the ShippingPackage associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function package()
    {
        return $this->hasOne(ShippingPackage::class, 'id', 'package_type_id');
    }

    /**
     * Get the Shipping Mode associated with the Shipment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mode()
    {
        return $this->hasOne(ShippingMode::class, 'id', 'shipping_mode');
    }
}
