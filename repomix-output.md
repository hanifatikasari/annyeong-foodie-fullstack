This file is a merged representation of a subset of the codebase, containing specifically included files, combined into a single document by Repomix.

# File Summary

## Purpose
This file contains a packed representation of a subset of the repository's contents that is considered the most important context.
It is designed to be easily consumable by AI systems for analysis, code review,
or other automated processes.

## File Format
The content is organized as follows:
1. This summary section
2. Repository information
3. Directory structure
4. Repository files (if enabled)
5. Multiple file entries, each consisting of:
  a. A header with the file path (## File: path/to/file)
  b. The full contents of the file in a code block

## Usage Guidelines
- This file should be treated as read-only. Any changes should be made to the
  original repository files, not this packed version.
- When processing this file, use the file path to distinguish
  between different files in the repository.
- Be aware that this file may contain sensitive information. Handle it with
  the same level of security as you would the original repository.

## Notes
- Some files may have been excluded based on .gitignore rules and Repomix's configuration
- Binary files are not included in this packed representation. Please refer to the Repository Structure section for a complete list of file paths, including binary files
- Only files matching these patterns are included: app/Database/Migrations/**/*, app/Config/Routes.php
- Files matching patterns in .gitignore are excluded
- Files matching default ignore patterns are excluded
- Files are sorted by Git change count (files with more changes are at the bottom)

# Directory Structure
```
app/Config/Routes.php
app/Database/Migrations/.gitkeep
app/Database/Migrations/20181211100537_install_ion_auth.php
app/Database/Migrations/2026-03-26-000003_TAbsensi.php
app/Database/Migrations/2026-03-26-010106_CreateCategoriesTable.php
app/Database/Migrations/2026-03-26-010141_CreateAttributesTable.php
app/Database/Migrations/2026-03-26-010213_CreateAttrributeOptionsTable.php
app/Database/Migrations/2026-03-26-010304_MBahanBaku.php
app/Database/Migrations/2026-03-26-010305_CreateProductsTable.php
app/Database/Migrations/2026-03-26-010306_MRecipes.php
app/Database/Migrations/2026-03-26-010308_TProduksi.php
app/Database/Migrations/2026-03-26-010309_TProduksiDetail.php
app/Database/Migrations/2026-03-26-010326_CreateProductCategoriesTable.php
app/Database/Migrations/2026-03-26-010340_CreateProductInventoriesTable.php
app/Database/Migrations/2026-03-26-010351_CreateProductAttributeValuesTable.php
app/Database/Migrations/2026-03-26-010403_CreateProductImagesTable.php
app/Database/Migrations/2026-04-03-043806_TPenjualan.php
app/Database/Migrations/2026-04-03-043817_TPenjualanDetail.php
app/Database/Migrations/2026-04-03-083330_TStokMasuk.php
app/Database/Migrations/2026-05-05-034623_CreateAttributeCategoriesTable.php
app/Database/Migrations/2026-05-12-122410_AddEcommerceFieldsToTPenjualan.php
app/Database/Migrations/2026-05-12-123139_AddCustomerProfileFieldsToUsers.php
app/Database/Migrations/2026-05-12-165544_CreateProductReviews.php
```

# Files

## File: app/Database/Migrations/.gitkeep
```

```

## File: app/Database/Migrations/20181211100537_install_ion_auth.php
```php
<?php
namespace App\Database\Migrations;

/**
 * CodeIgniter IonAuth
 *
 * @package CodeIgniter-Ion-Auth
 * @author  Benoit VRIGNAUD <benoit.vrignaud@zaclys.net>
 * @license https://opensource.org/licenses/MIT	MIT License
 * @link    http://github.com/benedmunds/CodeIgniter-Ion-Auth
 */

/**
 * Migration class
 *
 * @package CodeIgniter-Ion-Auth
 */
class Migration_Install_ion_auth_custom extends \CodeIgniter\Database\Migration
{
	/**
	 * Tables
	 *
	 * @var array
	 */
	private $tables;

	/**
	 * Construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		$config = config('IonAuth');

		// initialize the database
		$this->DBGroup = empty($config->databaseGroupName) ? '' : $config->databaseGroupName;

		parent::__construct();

		$this->tables = $config->tables;
	}

	/**
	 * Up
	 *
	 * @return void
	 */
	public function up()
	{
		// Drop table 'groups' if it exists
		$this->forge->dropTable($this->tables['groups'], true);

		// Table structure for table 'groups'
		$this->forge->addField([
			'id' => [
				'type'           => 'int',
				'constraint'     => '11',
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'name' => [
				'type'       => 'VARCHAR',
				'constraint' => '20',
			],
			'description' => [
				'type'       => 'VARCHAR',
				'constraint' => '100',
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable($this->tables['groups']);

		// Drop table 'users' if it exists
		$this->forge->dropTable($this->tables['users'], true);

		// Table structure for table 'users'
		$this->forge->addField([
			'id' => [
				'type'           => 'int',
				'constraint'     => '11',
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'ip_address' => [
				'type'       => 'VARCHAR',
				'constraint' => '45',
			],
			'username' => [
				'type'       => 'VARCHAR',
				'constraint' => '50',
			],
			'password' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
			],
			'email' => [
				'type'       => 'VARCHAR',
				'constraint' => '254',
				'unique'     => true,
			],
			'activation_selector' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
				'unique'     => true,
			],
			'activation_code' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
			],
			'forgotten_password_selector' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
				'unique'     => true,
			],
			'forgotten_password_code' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
			],
			'forgotten_password_time' => [
				'type'       => 'INT',
				'constraint' => '11',
				'unsigned'   => true,
				'null'       => true,
			],
			'remember_selector' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
				'unique'     => true,
			],
			'remember_code' => [
				'type'       => 'VARCHAR',
				'constraint' => '255',
				'null'       => true,
			],
			'created_on' => [
				'type'       => 'INT',
				'constraint' => '11',
				'unsigned'   => true,
			],
			'last_login' => [
				'type'       => 'INT',
				'constraint' => '11',
				'unsigned'   => true,
				'null'       => true,
			],
			'active' => [
				'type'       => 'datetime',
				'null'       => true,
			],
			'first_name' => [
				'type'       => 'VARCHAR',
				'constraint' => '30',
				'null'       => true,
			],
			'last_name' => [
				'type'       => 'VARCHAR',
				'constraint' => '30',
				'null'       => true,
			],
			
			'phone' => [
				'type'       => 'VARCHAR',
				'constraint' => '20',
				'null'       => true,
			],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
			
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable($this->tables['users'], false);

		// Drop table 'users_groups' if it exists
		$this->forge->dropTable($this->tables['users_groups'], true);

		// Table structure for table 'users_groups'
		$this->forge->addField([
			'id' => [
				'type'           => 'int',
				'constraint'     => '11',
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'user_id' => [
				'type'       => 'int',
				'constraint' => '11',
				'unsigned'   => true,
			],
			'group_id' => [
				'type'       => 'int',
				'constraint' => '11',
				'unsigned'   => true,
			],
		]);
		$this->forge->addKey('id', true);

		$this->forge->addForeignKey('user_id', $this->tables['users'], 'id', 'NO ACTION', 'CASCADE');
		$this->forge->addForeignKey('group_id', $this->tables['groups'], 'id', 'NO ACTION', 'CASCADE');

		$this->forge->createTable($this->tables['users_groups']);

		// Drop table 'login_attempts' if it exists
		$this->forge->dropTable($this->tables['login_attempts'], true);

		// Table structure for table 'login_attempts'
		$this->forge->addField([
			'id' => [
				'type'           => 'int',
				'constraint'     => '11',
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'ip_address' => [
				'type'       => 'VARCHAR',
				'constraint' => '45',
			],
			'login' => [
				'type'       => 'VARCHAR',
				'constraint' => '50',
				'null'       => true,
			],
			'time' => [
				'type'       => 'INT',
				'constraint' => '11',
				'unsigned'   => true,
				'null'       => true,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable($this->tables['login_attempts']);
	}

	/**
	 * Down
	 *
	 * @return void
	 */
	public function down()
	{
// Hapus ANAK dulu (tabel yang punya Foreign Key)
    $this->forge->dropTable($this->tables['users_groups'], true);
    $this->forge->dropTable($this->tables['login_attempts'], true);
    
    // Baru hapus INDUK
    $this->forge->dropTable($this->tables['users'], true);
    $this->forge->dropTable($this->tables['groups'], true);
	}
}
```

## File: app/Database/Migrations/2026-03-26-000003_TAbsensi.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TAbsensi extends Migration
{
    public function up()
    {
       $this->forge->addField([
        'id' => [
            'type'           => 'INT',
            'constraint'     => 11,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'user_id' => [ // Relasi ke tabel users GieArt87
            'type'       => 'int',
            'constraint' => 11,
            'unsigned'   => true,
        ],
        'jam_masuk' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
        'jam_keluar' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
        'total_jam' => [
            'type'       => 'DECIMAL',
            'constraint' => '4,2',
            'null'       => true,
         ],
        'keterangan' => [ // Misal: "Izin", "Sakit", atau "Lembur"
            'type'       => 'VARCHAR',
            'constraint' => 20,
            'null'       => true,
        ],
        
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);

    $this->forge->addKey('id', true);
    // Tambahkan Foreign Key ke tabel users
    $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
    $this->forge->createTable('t_absensi');
    }

    public function down()
    {
        $this->forge->dropTable('t_absensi');
    }
}
```

## File: app/Database/Migrations/2026-03-26-010106_CreateCategoriesTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
		$this->forge->addField([
			'id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'name' => [
				'type' => 'VARCHAR', //tepung, gula, daging
				'constraint' => 50,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 60,
			],
			'parent_id' => [ //Self-Referencing Relationship.[sub kategori]
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'prefix' => [
				'type'       => 'CHAR',
				'constraint' => 3, // Cukup 5 huruf: AYM, TPG, BUM
			],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
        	'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],  
		]);

		$this->forge->addKey('id', true);
		$this->forge->addKey('slug');
		$this->forge->addForeignKey('parent_id', 'categories', 'id', 'SET NULL', 'CASCADE');
		$this->forge->createTable('categories');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('categories');
	}
}
```

## File: app/Database/Migrations/2026-03-26-010141_CreateAttributesTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttributesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'smallint',
				'constraint' => 5,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'code' => [
				'type' => 'VARCHAR',
				'constraint' => 60,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
			],
			'type' => [
				'type'       => 'varchar',
				'constraint' => 20,
			],
			'validation' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
			],
			'is_required' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
			],
			'is_unique' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
			],
			'is_filterable' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
			],
			'is_configurable' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => false,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addUniqueKey('code');
		$this->forge->createTable('attributes');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('attributes');
	}
}
```

## File: app/Database/Migrations/2026-03-26-010213_CreateAttrributeOptionsTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttrributeOptionsTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'smallint',
				'constraint' => 5,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'attribute_id' => [
				'type' => 'smallint',
				'constraint' => 5,
				'unsigned' => true,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 60,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addKey('slug');
		$this->forge->addForeignKey('attribute_id','attributes','id','CASCADE','CASCADE');
		$this->forge->createTable('attribute_options');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
		$this->forge->dropTable('attribute_options');
	}
}
```

## File: app/Database/Migrations/2026-03-26-010304_MBahanBaku.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MBahanBaku extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'mediumint',
                'constraint'     => 8,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_bahan' => [ // Ini yang formatnya AYM-001, TPG-001, BUM-001 dst pakai prefix di categories
                'type'       => 'CHAR',
                'constraint' => 7,
                'unique'     => true, // Supaya tidak ada kode ganda
            ],
            'category_id' => [
                'type'           => 'MEDIUMINT',
                'constraint'     => 8, //ambil dari categories misal id=10 (tepung) id=11 (daging) 
                'unsigned'       => true,
                'null'           => true, // Set null jika tidak wajib diisi
            ],
            'nama_bahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 5, // Kg, Gram, Pcs, pack
            ],
            'harga_beli_satuan' => [
                'type'       => 'mediumint',
                'constraint' => '8', //menampung sampai 16jt
                'default'    => 0,
            ],
                'stok_sekarang' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '8,2', //999 ribuan
                    'default'    => 0,
            ],
            'stok_minimal' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2', //9 ribuan
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],    
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'SET NULL', 'CASCADE');
        //............addForeignKey(kolom_ini, tabel_tujuan, kolom_tujuan, ON_DELETE, ON_UPDATE).
        $this->forge->createTable('m_bahan_baku');
    }

    public function down()
    {
        $this->forge->dropTable('m_bahan_baku');
    }
}
```

## File: app/Database/Migrations/2026-03-26-010305_CreateProductsTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'mediumint', //16jt
				'constraint' => 8,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'parent_id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'user_id' => [
				'type'           => 'int',
				'constraint'     => 11,
				'unsigned'       => true,
				'null' => true,
			],
			
			'published_at' => [
				'type' => 'DATETIME',
				'null' => true, // Jika NULL berarti produk non-aktif/draft
		
			],
			'sku' => [ //kode untuk produk jadi -> MCH-0001
				'type' => 'CHAR',
				'constraint' => 8,
			],
			'type' => [
				'type' => 'CHAR',
				'constraint' => 8,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 120,
			],
			'price' => [
				'type' => 'mediumint',
				'constraint' => '8', //maksimal 16jt
				'unsigned' => true,
			],
			'hpp_total' => [ // auto filled dari perhitungan resep (untuk hitung laba bersih)
                'type' => 'mediumint',
                'constraint' => '8',
                'default' => 0,
            ],
			'weight' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'length' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'width' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'height' => [
				'type' => 'DECIMAL',
				'constraint' => '5,2',
				'null' => true,
			],
			'short_description' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => true,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => true,
			],
			 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addKey('slug');
		$this->forge->addKey('sku');
		$this->forge->addKey('parent_id');
		$this->forge->addForeignKey('user_id','users','id','SET NULL','CASCADE');
		$this->forge->createTable('products');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('products'); 
	}
}
```

## File: app/Database/Migrations/2026-03-26-010306_MRecipes.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MRecipes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'mediumint',
                'constraint'     => 8,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [ // Menghubungkan ke tabel products (Dimsum Mentai)
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'bahan_baku_id' => [ // Menghubungkan ke m_bahan_baku (Ayam, tepung, dll)
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'jumlah_kebutuhan' => [ 
                // Contoh: 999,999 
                'type'       => 'DECIMAL',
                'constraint' => '6,3', 
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
           	 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        
        // Mencegah duplikasi bahan yang sama dalam satu resep produk
        $this->forge->addUniqueKey(['product_id', 'bahan_baku_id']);

        // Foreign Key agar data konsisten
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE'); //cascade brti jika data di master dihapus maka data di tabel lain ikut terhapus
        $this->forge->addForeignKey('bahan_baku_id', 'm_bahan_baku', 'id', 'RESTRICT', 'CASCADE'); 
        //Skenario: Kamu punya bahan baku "Ayam Fillet" (ID: 5) di tabel m_bahan_baku. Bahan ini sedang digunakan di resep "Dimsum Mentai", "Dimsum Ayam"
        //jika bahan di hapus maka resep dimsum akan kehilangan komponen bahan baku nya karena bahan nomor id 5 sudah dihapus
        //maka restrict akan memunculkan error di database untuk menolak penghapusan tersebut

        $this->forge->createTable('m_recipes');
    }

    public function down()
    {
        $this->forge->dropTable('m_recipes');
    }
}
```

## File: app/Database/Migrations/2026-03-26-010308_TProduksi.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TProduksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT', //Jika sehari ada 10 kali produksi, setahun sudah 3.650 baris. Dalam beberapa tahun, angka ini bisa melewati batas MEDIUMINT.
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_produksi' => [ 
                'type'       => 'CHAR', // misal: PRD-20260409-001 --->tgl Standard Internasional (ISO 8601)
                'constraint' => 16,
                'unique'     => true, // Harus unik, tidak boleh sama antar transaksi, buat otomatis di controller
            ],
            'product_id' => [ // Menu apa yang dibuat?
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'user_id' => [ // Siapa yang memproduksi?
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, //set null jiks user dihapus
            ],
            'qty_hasil' => [ // Berapa porsi yang jadi?
                'type'       => 'smallint', //hingga 65.535
                'constraint' => 5,
                'default'    => 0,
            ],
            'tanggal_produksi' => [
                'type' => 'DATE',
            ],
            'status_qc' => [ // Quality Control: 'Lolos' atau 'Gagal/Reject'
                'type'       => 'ENUM',
                'constraint' => ['Lolos', 'Reject'],
                'default'    => 'Lolos',
            ],
            'catatan' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => true,
            ],
             'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        // Relasi ke tabel Master
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('t_produksi');
    }

    public function down()
    {
        $this->forge->dropTable('t_produksi');
    }
}
```

## File: app/Database/Migrations/2026-03-26-010309_TProduksiDetail.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TProduksiDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'produksi_id' => [ // Menunjuk ke t_produksi (Masak apa hari ini?)
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bahan_baku_id' => [ // Bahan apa yang dipakai?
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'qty_digunakan' => [ 
                // Hasil hitungan: (Jumlah di Resep) x (Total Produksi)
                // contoh resep butuh 0.250 kg ayam untuk 1 porsi, lalu hari ini produksi 100 porsi, maka qty_digunakan = 0.250 x 100 = 25 kg ||misal satuan gak mgkn >9k perhari
                //satuan ambil pakai join ke tabel m_bahan_baku untuk ambil satuan (kg, liter, dll)
                'type'       => 'DECIMAL',
                'constraint' => '7,3',
            ],
        	'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // Foreign Keys
        $this->forge->addForeignKey('produksi_id', 't_produksi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bahan_baku_id', 'm_bahan_baku', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('t_produksi_detail');
    }

    public function down()
    {
        $this->forge->dropTable('t_produksi_detail');
    }
}
```

## File: app/Database/Migrations/2026-03-26-010326_CreateProductCategoriesTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductCategoriesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'product_id' => [
				'type'           => 'mediumint',
				'constraint'     => 8,
				'unsigned'       => true,
			],
			'category_id' => [
				'type'           => 'mediumint',
				'constraint'     => 8,
				'unsigned'       => true,
			],
			 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
			 'deleted_at' => ['type' => 'DATETIME', 'null' => true],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('product_id','products','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('category_id','categories','id','CASCADE','CASCADE');
		$this->forge->createTable('product_categories');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_categories');
	}
}
```

## File: app/Database/Migrations/2026-03-26-010340_CreateProductInventoriesTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductInventoriesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'product_id' => [
				'type'           => 'mediumint',
				'constraint'     => 8,
				'unsigned'       => true,
			],
			'qty' => [ // Saldo stok saat ini
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
			],
			'low_stock_threshold' => [ // Batas stok menipis (biar dapet alert)
				'type'       => 'smallint', //65rb
				'constraint' => 5,
				'unsigned'   => true,
				'default'    => 10,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('product_id','products','id');
		$this->forge->createTable('product_inventories');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_inventories');
	}
}
```

## File: app/Database/Migrations/2026-03-26-010351_CreateProductAttributeValuesTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductAttributeValuesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT', //ini tabel transaksi
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'parent_product_id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'product_id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'attribute_id' => [
				'type'           => 'smallint',
				'constraint'     => 5,
				'unsigned'       => true,
				'null' => true,
			],
			'attribute_option_id' => [
				'type'           => 'smallint',
				'constraint'     => 5,
				'unsigned'       => true,
				'null' => true,
			],
			'text_value' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'boolean_value' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => true,

			],
			'integer_value' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => true,
			],
			'float_value' => [
				'type' => 'DECIMAL',
				'constraint' => '6,3',
				'null' => true,
			],
			'datetime_value' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'date_value' => [
				'type' => 'DATE',
				'null' => true,
			],
			'json_value' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('attribute_option_id','attribute_options','id');
		$this->forge->addForeignKey('attribute_id','attributes','id');
		$this->forge->addForeignKey('product_id','products','id');
		$this->forge->addForeignKey('parent_product_id','products','id');
		$this->forge->createTable('product_attribute_values');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_attribute_values');
	}
}
```

## File: app/Database/Migrations/2026-03-26-010403_CreateProductImagesTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductImagesTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'product_id' => [
				'type' => 'mediumint',
				'constraint' => 8,
				'unsigned' => true,
				'null' => false,
			],
			'original' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'large' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'medium' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'small' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'created_at' => [
				'type' => 'DATETIME',
			],
			'updated_at' => [
				'type' => 'DATETIME',
			],
		]);

		$this->forge->addKey('id', true);
		$this->forge->addForeignKey('product_id', 'products', 'id');
		$this->forge->createTable('product_images');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('product_images');
	}
}
```

## File: app/Database/Migrations/2026-04-03-043817_TPenjualanDetail.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TPenjualanDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'penjualan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'product_id' => [
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'qty' => [
                'type'       => 'smallint', //65rb
                'constraint' => 5,
                'unsigned'   => true,
            ],
            'hpp_price' => [ // Harga modal (untuk hitung laba bersih)
                'type'       => 'mediumint',
                'constraint' => '8',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'selling_price' => [ // Penting! Kalau suatu saat harga naik, data lama gak berubah
                'type'       => 'mediumint',
                'constraint' => '8',
                'unsigned'   => true,
            ],
            'subtotal' => [
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true    ,
                'default'    => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('penjualan_id', 't_penjualan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('t_penjualan_detail');
    }

    public function down()
    {
        $this->forge->dropTable('t_penjualan_detail');
    }
}
```

## File: app/Database/Migrations/2026-04-03-083330_TStokMasuk.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TStokMasuk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bahan_baku_id' => [ // Mengambil 'id' dari m_bahan_baku
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'qty' => [
                'type'       => 'mediumint', //16jt
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'isi_per_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2', // Pakai decimal agar aman untuk angka besar/koma
                'default'    => 1,      // Default 1, artinya eceran (1 x Qty)
                'null'       => false,
            ],
            'harga_satuan' => [
                'type'       => 'mediumint',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'total_harga' => [ // qty * harga_satuan (Dihitung di controller)
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'nama_supplier' => [ // Tidak perlu tabel tambahan, cukup ketik manual
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'keterangan' => [ // Untuk catatan tambahan
                'type' => 'varchar',
                'constraint' => 150,
                'null' => true,
            ],
           	'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        // Relasi: Jika bahan baku dihapus (Restrict), stok masuk tidak bisa yatim piatu
        $this->forge->addForeignKey('bahan_baku_id', 'm_bahan_baku', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('t_stok_masuk');
        }

    public function down()
    {
        $this->forge->dropTable('t_stok_masuk');
    }
}
```

## File: app/Database/Migrations/2026-05-05-034623_CreateAttributeCategoriesTable.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttributeCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'mediumint',
                'constraint' => 8,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'attribute_id' => [
                'type' => 'SMALLINT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        

        // Foreign Keys
        $this->forge->addForeignKey(
            'attribute_id',
            'attributes',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'category_id',
            'categories',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Obiar tidak duplicate
        $this->forge->addUniqueKey(['attribute_id', 'category_id']);

        $this->forge->createTable('attribute_categories');
    }

    public function down()
    {
        $this->forge->dropTable('attribute_categories');
    }
}
```

## File: app/Database/Migrations/2026-05-12-122410_AddEcommerceFieldsToTPenjualan.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEcommerceFieldsToTPenjualan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('t_penjualan', [
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'order_status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'pending',
                    'diproses',
                    'dikirim',
                    'selesai',
                    'dibatalkan',
                ],
                'default' => 'pending',
                'after'   => 'customer_id',
            ],

            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'menunggu_pembayaran',
                    'menunggu_verifikasi',
                    'lunas',
                ],
                'default' => 'menunggu_pembayaran',
                'after'   => 'order_status',
            ],

            'payment_proof' => [ //menyimpan nama file bukti pembayaran (jika metode bukan Cash)
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'payment_status',
            ],

            'shipping_address' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'payment_proof',
            ],

            'catatan_customer' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'shipping_address',
            ],

            'verified_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'catatan_customer',
            ],

            'verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'verified_at',
            ],
        ]);

        // Foreign key customer_id -> users.id
         $this->db->query("
            ALTER TABLE t_penjualan
            ADD CONSTRAINT fk_t_penjualan_customer
            FOREIGN KEY (customer_id)
            REFERENCES users(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
        
         // Foreign key verified_by -> users.id
        $this->db->query("
            ALTER TABLE t_penjualan
            ADD CONSTRAINT fk_t_penjualan_verified_by
            FOREIGN KEY (verified_by)
            REFERENCES users(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        // Hapus foreign key terlebih dahulu
         $this->db->query("
            ALTER TABLE t_penjualan
            DROP FOREIGN KEY fk_t_penjualan_customer
        ");

        $this->db->query("
            ALTER TABLE t_penjualan
            DROP FOREIGN KEY fk_t_penjualan_verified_by
        ");

        $this->forge->dropColumn('t_penjualan', [
            'customer_id',
            'order_status',
            'payment_status',
            'payment_proof',
            'shipping_address',
            'catatan_customer',
            'verified_at',
            'verified_by',
        ]);
    
    }
}
```

## File: app/Database/Migrations/2026-05-12-123139_AddCustomerProfileFieldsToUsers.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerProfileFieldsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'address' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'phone',
            ],

            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'address',
            ],

            'province' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'city',
            ],

            'postal_code' => [
                'type'       => 'char',
                'constraint' => 5,
                'null'       => true,
                'after'      => 'province',
            ],

            'avatar' => [ // menyimpan nama file avatar/profile picture
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'postal_code',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', [
            'address',
            'city',
            'province',
            'postal_code',
            'avatar',
        ]);
    }
}
```

## File: app/Database/Migrations/2026-05-12-165544_CreateProductReviews.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductReviews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 5,
            ],
            'ulasan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addUniqueKey(['product_id', 'user_id']); //berarti satu user 
        // hanya boleh memberikan satu review untuk satu produk.
//
        $this->forge->addForeignKey(
            'product_id',
            'products',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('product_reviews');
    }

    public function down()
    {
        $this->forge->dropTable('product_reviews');
    }
}
```

## File: app/Database/Migrations/2026-04-03-043806_TPenjualan.php
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TPenjualan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'invoice_no' => [ // nomor faktur. Contoh: AES-20260404-001
                'type'       => 'CHAR',
                'constraint' => 16,
                'unique'     => true,
            ],
            'total_harga' => [
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'diskon' => [ // Potongan harga (jika ada promo)
                'type'       => 'mediumint',
                'constraint' => '8',
                'default'    => 0,
            ],
            'total_bayar' => [ // Harga akhir (total_harga - diskon)
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['Cash', 'QRIS', 'Transfer'],
                'default'    => 'Cash',
            ],
            'uang_diterima' => [ // Uang yang dikasih pelanggan (untuk hitung kembalian)
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'kasir_id' => [ // Siapa yang jaga kasir?
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
            ],
			 'created_at' => ['type' => 'DATETIME', 'null' => true],
        	 'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kasir_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('t_penjualan');
        }

    public function down()
    {
        $this->forge->dropTable('t_penjualan');
    }
}
```

## File: app/Config/Routes.php
```php
<?php namespace Config;

$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// 1. FRONTEND (E-BISNIS) - TANPA FILTER
$routes->get('/', 'Home::index');
$routes->get('products', 'Products::index');
$routes->get('products/(:segment)/(:segment)', 'Products::show/$1/$2');

// 2. AUTHENTICATION
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('forgot_password', 'Auth::forgot_password');
});

// 3. ADMIN AREA (ERP/Dashboard) - PAKAI FILTER
$routes->group('admin', ['filter' => 'authAdmin:admin,pemilik,gudang,produksi,penjualan'], function($routes) {
    
    $routes->get('dashboard', 'Admin\Dashboard::index');

     // --- CATEGORIES ---
    // Produksi & Penjualan butuh ini untuk manajemen katalog
    $routes->group('categories', ['filter' => 'authAdmin:admin,penjualan,gudang,pemilik,produksi'], function($routes) {
        $routes->get('/', 'Admin\Categories::index');
        
        $routes->group('', ['filter' => 'authAdmin:admin,penjualan,gudang'], function($routes) {
            $routes->post('simpan', 'Admin\Categories::simpan');
            $routes->get('edit/(:num)', 'Admin\Categories::edit/$1');
            $routes->get('hapus/(:num)', 'Admin\Categories::hapus/$1');
        });
    });

    // --- PRODUCTS ---
    $routes->group('products', ['filter' => 'authAdmin:admin,produksi,pemilik'], function($routes) {
    // Tampilan List & Sampah
    $routes->get('/', 'Admin\Products::index');
    $routes->get('trashed', 'Admin\Products::trashed');

        // Fitur Khusus Admin/Produksi
        $routes->group('', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
            $routes->get('create', 'Admin\Products::create');
            $routes->post('/', 'Admin\Products::store'); // Simpan baru
            $routes->get('edit/(:num)', 'Admin\Products::edit/$1');
            $routes->put('(:num)', 'Admin\Products::update/$1'); // Update data (Method PUT)
            $routes->delete('(:num)', 'Admin\Products::destroy/$1'); // Hapus
            
            
            // Fitur Gambar 
            $routes->get('(:num)/images', 'Admin\Products::images/$1');
            $routes->get('(:num)/upload-image', 'Admin\Products::uploadImage/$1');
            $routes->post('(:num)/upload-image', 'Admin\Products::doUploadImage/$1');
            $routes->delete('images/(:num)', 'Admin\Products::destroyImage/$1');
            
            $routes->get('getCategoriesAjax', 'Admin\Products::getCategoriesAjax');
            $routes->post('getAttributesByCategory', 'Admin\Products::getAttributesByCategory');
        });
    });


    // --- PRODUKSI ---
    $routes->group('produksi', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
        $routes->get('/', 'Admin\Produksi::index');
        $routes->get('tambah', 'Admin\Produksi::tambah');
        $routes->post('simulasi', 'Admin\Produksi::simulasi');
        $routes->post('simpan', 'Admin\Produksi::simpan');
        $routes->get('show/(:num)', 'Admin\Produksi::show/$1');
    });

    $routes->group('admin', ['filter' => 'authAdmin:admin,penjualan,pemilik'], function($routes) {
        $routes->get('orders', 'Admin\Orders::index');
        $routes->get('orders/detail/(:num)', 'Admin\Orders::detail/$1');
        $routes->post('orders/verify/(:num)', 'Admin\Orders::verify/$1');
        $routes->post('orders/update-status/(:num)', 'Admin\Orders::updateStatus/$1');
    });

    // --- PENJUALAN ---
    $routes->group('penjualan', ['filter' => 'authAdmin:admin,penjualan'], function($routes) {
        $routes->get('/', 'Admin\Penjualan::index');
        $routes->get('create', 'Admin\Penjualan::create');
        $routes->post('simpan', 'Admin\Penjualan::simpan');
        $routes->get('show/(:num)', 'Admin\Penjualan::show/$1');
        });

        // Orders
    $routes->get('orders', 'Admin\Orders::index');
    $routes->get('orders/detail/(:num)', 'Admin\Orders::detail/$1');
    $routes->post('orders/verify/(:num)', 'Admin\Orders::verify/$1');
    $routes->post('orders/update-status/(:num)', 'Admin\Orders::updateStatus/$1');
    $routes->post('orders/updateStatus/(:num)', 'Admin\Orders::updateStatus/$1');
    

    // --- REPORTS ---
    $routes->group('reports', ['filter' => 'authAdmin:admin,pemilik'], function($routes) {
        $routes->get('/', 'Admin\Reports::index');
        $routes->get('penjualan', 'Admin\Reports::penjualan');
        $routes->get('produksi', 'Admin\Reports::produksi');
        $routes->get('stok-bahan', 'Admin\Reports::stokBahan');
    });
    
    // --- BAHAN BAKU (Inventory) ---
    // Pemilik bisa LIHAT, tapi cuma Admin & Gudang yang bisa eksekusi CRUD
    $routes->group('bahanbaku', ['filter' => 'authAdmin:admin,gudang,pemilik'], function($routes) {
        $routes->get('/', 'Admin\BahanBaku::index'); // Read-only untuk pemilik
        
        $routes->group('', ['filter' => 'authAdmin:admin,gudang'], function($routes) {
            $routes->get('tambah', 'Admin\BahanBaku::tambah');
            $routes->post('simpan', 'Admin\BahanBaku::simpan');
            $routes->get('edit/(:num)', 'Admin\BahanBaku::edit/$1');
            $routes->post('update/(:num)', 'Admin\BahanBaku::update/$1');
            $routes->get('hapus/(:num)', 'Admin\BahanBaku::hapus/$1');
            $routes->get('getCategoriesAjax', 'Admin\BahanBaku::getCategoriesAjax');
        });
    });

        // --- ATTRIBUTES & OPTIONS ---
    $routes->group('attributes', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
        // Utama: Management Atribut
        $routes->get('/', 'Admin\Attributes::index');
        $routes->get('create', 'Admin\Attributes::create'); // Opsional jika form tambah di halaman beda
        $routes->get('edit/(:num)', 'Admin\Attributes::edit/$1'); 
        $routes->post('store', 'Admin\Attributes::store');
        $routes->post('update/(:num)', 'Admin\Attributes::update/$1');
        $routes->get('delete/(:num)', 'Admin\Attributes::destroy/$1');
        $routes->get('getCategoriesAjax', 'Admin\Attributes::getCategoriesAjax');

        $routes->group('(:num)/options', function($routes) {
            $routes->get('/', 'Admin\AttributeOptions::index/$1');
            $routes->post('store', 'Admin\AttributeOptions::store/$1');
            
            // Edit butuh 2 parameter: ID Attribute dan ID Option
            $routes->get('edit/(:num)', 'Admin\AttributeOptions::index/$1/$2');
            $routes->post('update/(:num)', 'Admin\AttributeOptions::update/$1/$2');
            
            // Delete butuh 2 parameter: ID Attribute dan ID Option
           $routes->get('delete/(:num)', 'Admin\AttributeOptions::destroy/$1/$2');
        });
    });

    // --- RECIPES ---
    $routes->group('recipes', ['filter' => 'authAdmin:admin,produksi'], function($routes) {
        $routes->get('/', 'Admin\Recipes::index');
        $routes->get('detail/(:num)', 'Admin\Recipes::detail/$1');
        $routes->post('simpan/(:num)', 'Admin\Recipes::simpan/$1');
        $routes->post('update/(:num)/(:num)', 'Admin\Recipes::update/$1/$2');
        $routes->get('hapus/(:num)/(:num)', 'Admin\Recipes::hapus/$1/$2');
        $routes->get('getBahanAjax', 'Admin\Recipes::getBahanAjax');
    });

    // --- STOK MASUK (Inventory) ---
    $routes->group('stokmasuk', ['filter' => 'authAdmin:admin,gudang,pemilik'], function($routes) {
        $routes->get('/', 'Admin\StokMasuk::index');

        $routes->group('', ['filter' => 'authAdmin:admin,gudang'], function($routes) {
             $routes->get('tambah', 'Admin\StokMasuk::tambah');
             $routes->post('simpan', 'Admin\StokMasuk::simpan');
             $routes->get('getBahanbakuAjax', 'Admin\StokMasuk::getBahanbakuAjax');
        });  
    });
    
    
});

// ============================================================
    // E-COMMERCE FRONTEND ROUTES
    // ============================================================

    // Product search
    $routes->get('search', 'Shop::search');

    // Cart
    $routes->get('cart',                   'Cart::index');
    $routes->post('cart/add',              'Cart::add');
    $routes->post('cart/update',           'Cart::update');
    $routes->post('cart/remove',           'Cart::remove');
    $routes->get('cart/clear',             'Cart::clear');

    // Checkout & Orders (wajib login)
    $routes->group('checkout', ['filter' => 'authFrontend'], function($routes) {
        $routes->get('/',                  'Checkout::index');
        $routes->post('process',           'Checkout::process');
        $routes->get('success/(:segment)', 'Checkout::success/$1');
        $routes->get('upload/(:segment)',  'Checkout::uploadForm/$1');
        $routes->post('upload/(:segment)', 'Checkout::uploadBukti/$1');
    });

    // =========================
    // CUSTOMER ACCOUNT
    // =========================
    $routes->group('account', ['filter' => 'authFrontend'], function($routes) {
        $routes->get('/', 'Account::index');
        $routes->get('orders', 'Account::orders');

         // Route spesifik harus lebih dulu
        $routes->get('orders/detail/(:num)', 'Account::orderDetail/$1');

        // Route alternatif lama
        $routes->get('orders/(:segment)', 'Account::orderDetail/$1');

        $routes->get('profile', 'Account::profile');
        $routes->post('profile/update', 'Account::updateProfile');
        $routes->get('track', 'Track::index');
    });
    

    // Track order (publik)
    $routes->get('track',                  'Shop::track');
    $routes->post('track',                 'Shop::trackOrder');

    // About
    $routes->get('about',                  'Shop::about');

    // Reviews
    $routes->post('products/review',       'Shop::submitReview');

    // ============================================================
    // ADMIN EXTENSION - Payment & Order Management
    // ============================================================
    $routes->group('admin', ['filter' => 'authAdmin:admin,penjualan,pemilik'], function($routes) {
        $routes->get('orders',                          'Admin\Orders::index');
        $routes->get('orders/(:num)',                   'Admin\Orders::detail/$1');
        $routes->post('orders/verify/(:num)',           'Admin\Orders::verify/$1');
        $routes->post('orders/status/(:num)',           'Admin\Orders::updateStatus/$1');
        $routes->get('orders/bukti/(:segment)',         'Admin\Orders::viewBukti/$1');
    });

    // ============================================================
    // FRONTEND CHECKOUT ROUTES (wajib login)
    // ============================================================
    $routes->group('checkout', ['filter' => 'authFrontend'], function ($routes) {
        $routes->get('/', 'Checkout::index');
        $routes->match(['get', 'post'], 'process', 'Checkout::process');
        $routes->match(['get', 'post'], 'upload/(:segment)', 'Checkout::upload/$1');
    });
```
