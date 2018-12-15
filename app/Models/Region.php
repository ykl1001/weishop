<?php namespace YiZan\Models;

class Region extends Base {
	protected $visible = ['id', 'name', 'is_default', 'firstChar', 'sort', 'level', 'pid','is_service','citylocation','py'];

	protected $appends = array('firstChar');

	protected $casts = [
	    'isDefault' => 'boolean',
	];

	public function getFirstCharAttribute() {
	    return strtoupper($this->attributes['py']{0});
	}

    public function citylocation(){
        return $this->belongsTo('YiZan\Models\CityLocation','id','id');
    }
}
