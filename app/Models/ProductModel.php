<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table      = 'products';
	protected $primaryKey = 'id';

	protected $returnType     = 'App\Entities\Product';

	protected $allowedFields = [
		'parent_id',
		'user_id',
		'published_at',
		'sku',
		'type',
		'name',
		'slug',
		'price',
		'hpp_total',
		'weight',
		'length',
		'width',
		'height',
		'short_description',
		'description',
		'deleted_at',
		
	];

	protected $useTimestamps = true;
	protected $useSoftDeletes = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';
	protected $deletedField  = 'deleted_at';

	protected $validationRules    = [
		'type' => 'required',
		'name'	=> 'required|min_length[3]',
		'sku' => 'required|is_unique[products.sku,id,{id?}]',
		'price' => 'permit_empty', //configurable parent kadang memang tidak punya harga. adanya di simple productnya
		'short_description' => 'permit_empty|max_length[100]'
		
	];

	protected $validationMessages = [];
	protected $skipValidation     = false;

	protected $beforeInsert = ['generateSlug'];

	const SIMPLE = 'simple';
	const CONFIGURABLE = 'configurable';

	const TYPES = [
		self::SIMPLE => 'Simple',
		self::CONFIGURABLE => 'Configurable',
	];

	const DRAFT = 'draft';
	const ACTIVE = 'active';

	const STATUSES = [
		self::DRAFT  => 'Draft (Tidak Tampil)',
		self::ACTIVE => 'Active (Tampil di Katalog)',
	];

	protected function generateSlug(array $data)
	{
		$slug = strtolower(url_title($data['data']['name']));
		$name = trim($data['data']['name']);

		$product = $this->where('name', $name)->orderBy('id', 'DESC')->first();
		if ($product) {
			$slugs = explode('-', $product->slug);
			$slugNumber = !(empty($slugs[1])) ? ((int)$slugs[1] + 1) : 1;
			$slug = $slug. '-' .$slugNumber;
		}

		$data['data']['slug'] = $slug;

		return $data;
	}

	public static function getProductTypesDropdown()
	{
		$types = array_merge(
			[
				'' => '-- Set product type --'
			],
			self::TYPES
		);
		return $types;
	}

	public static function getStatuses()
	{
		$statuses = array_merge(
			[
				'' => '-- Set Status --',
				self::DRAFT  => 'Save as Draft',
        		self::ACTIVE => 'Display in Catalog',
			],
			
		);

		return $statuses;
	}
	
}
