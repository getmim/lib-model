# lib-model

Manager untuk model aplikasi. Module ini tidak bisa berdiri sendiri, harus
ada module handler/driver yang bertugas melanjutkan tugas ke database yang
bersangkutan. Salah satu module yang bisa digunakan sebagai driver adalah
`lib-model-mysql`.

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-model
```

## Konfigurasi

Semua konfigurasi koneksi, dan read/write connection di definisikan seperti
di bawah:

```php
return [
    // ...
    'libModel' => [
        'connections' => [
            '/connection_name/' => [
                'driver' => '/driver_name/',
                'configs' => [
                    // connection configs
                    [...],
                    [...]
                ]
            ],
            'default' => [
                'driver' => 'mysql',
                'configs' => [
                    [
                        'host'   => 'locahost',
                        // ...
                    ],
                    [
                        'host' => 'localhost',
                        // ...
                    ]
                ]
            ]
        ],

        'model' => [
            'Model\\Name' => 'default',
            'Model\\Name\\Other' => [
                'read' => 'default',
                'write' => 'default2'
            ],
            'Model\\Name\\*' => [
                'read' => 'default'
            ]
        ],

        'target' => [
            'read' => 'default2',
            'write' => 'other-connection'
        ]
    ]
    // ...
];
```

Konfigurasi `connections` berisi informasi driver yang akan digunakan,
dan konfigurasi koneksi driver. Nilai `driver` menentukan driver apa yang digunakan
untuk handler koneksi ini, sementara properti `configs` berisi daftar opsi koneksi.
Jika opsi koneksi pertama gagal, maka koneksi selanjutnya akan dicoba.

Konfigurasi `model` menentukan driver/konenksi mana yang digunakan oleh suatu model.
Nilai ini adalah `model->connection_name` pair yang mana `connection_name` bisa berupa
string berisi nama koneksi untuk `read` dan `write`. Atau bisa juga array untuk mendefinisikan
masing-masing koneksi untuk `read` dan `write`. Jika menggunakan array, dan hanya salah
satu yang ditentukan, maka sisanya mengambil dari konfigurasi `target`. Nilai model name
mungkin mengandung karakter `*` yang berarti semua model dianggap cocok kecuali yang sudah
ditentukan.

Opsi ketiga adalah `target`, yang menentukan default koneksi untuk `read` dan `write`.
Jika target koneksi suatu model tidak didefinisikan, atau hanya mendefinisikan satu
saja, maka sisanya diambil dari konfigurasi ini. Sebagai catatan, suatu model harus
memiliki driver yang sama untuk `read` dan `write`.

## Standar Model

Semua model harus meng-extends dari `Mim\Model`. Masing-masing model juga harus memiliki
properti-properti berikut:

1. `chains::array` Daftar hubungan masing-masing field tabel ini dengan field
tabel yang lain.
1. `q::array`  Daftar field yang akan dicocokan dengan operator `LIKE` jika pada
kondisi where terdapat field `q`.
1. `table::string` Nama tabel yang ditangani model ini.

Keterangan lebih lanjut tentang masing-masing properti ini ada pada pembahasan `Standar Model`.

## Custom Driver

Karena modul ini adalah manager, maka dia membutuhkan module yang bertugas langsung
menghubungkan aplikasi dengan database. Driver adalah class yang bertugas untuk
melakukan handler tersebut. Semua driver harus mengimplementasikan interface
`LibModel\Iface\Driver`.

Keterangan lebih lanjut tentang custom driver bisa di lihat di `Custom Driver`.

## Penggunaan

Begitu suatu model didefinisikan, model tersebut kemudian bisa digunakan dari 
aplikasi seperti contoh di bawah:

```php
use LibUser\Model\User;

$user_id = User::create(['name'=>'Mim']);
$user = User::getOne(['id'=>$user_id]);
```

Keterangan lebih lanjut tentang cara penggunaan bisa dilihat di `Penggunaan`.
Dan informasi lebih jelas tetang parameter `$where` bisa dilihat di `Kondisi Where`.

## Formatter

Jika module `lib-formatter` terpasang, beberapa tambahan type format didefinisikan oleh
module ini seperti `multiple-object`, `object`, `chain` dan `partial`. Informasi lebih
jelas tentang formatter bisa dilihat di `Model Formatter`.

## Validator

Jika module `lib-validator` terpasang, maka module ini mendaftarkan satu tipe validasi
dengan nama `unique` untuk mengecek keberadaan suatu data pada table untuk tujuan
verifikasi.

## Migrasi

Module menerima route dari cli dengan perintah sebagai berikut:

```
mim [--table=..,..] migrate test
mim [--table=..,..] migrate start
mim [--table=..,..] migrate schema (dirname)
```

Keterangan lebih lanjut tentang perintah ini ada pada `Migrasi Model`.