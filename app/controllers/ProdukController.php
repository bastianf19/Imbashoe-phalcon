<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;

use App\Models\Produk as Produk;

use App\Validation\ProdukValidation as ProdukValidation;

class ProdukController extends ControllerBase
{
    public function indexAction()
    {

    }
    public function cariAction()
    {
        $cari_nama = $this->request->getPost('nama');
        $this->view->nama_prod = $cari_nama;
        $cari_nama = '%'.$cari_nama.'%';
        // echo $cari_nama;
        $produk = Produk::query()
            ->where('nama_produk LIKE :cari_nama:')
            ->bind(
                [
                    'cari_nama' => $cari_nama,
                ]
            )
            ->execute();
        $this->view->cari = $produk;
        // $this->response->redirect('/produk/cari');
    }
    public function tambahAction()
    {
        $produk = new Produk();

        //assign value from the form to $user
        $produk->assign(
            $this->request->getPost(),
            [
                'nama_produk',
                'brand_produk',
                'deskripsi_produk',
                'harga_produk',
                'status_produk',
            ]
        );
        if($this->request->hasFiles())
        {
            $img = $this->request->getUploadedFiles()[0];
            $path = 'img/'.$img->getName();
            $produk->foto_produk = $path;
            $img->moveTo($path);
        }

        // Store and check for errors
        $success = $produk->save();

        // passing the result to the view
        $this->view->success = $success;

        if ($success) {
            $message = "Produk berhasil ditambahkan.";
        } else {
            $message = "Terdapat kesalahan ketika menambahkan produk:<br>"
                     . implode('<br>', $produk->getMessages());
        }

        // passing a message to the view
        $this->view->message = $message;
    }
    public function listAction()
    {
        $produk = new Produk();
        $this->view->produk = Produk::find();
    }
    public function editAction($id_produk)
    {
        $editProduk = Produk::findFirstByid_produk($id_produk);
        $this->view->produk = $editProduk;
        echo $this->tag->linkTo(['/produk/list', 'List Produk', 'class' => 'btn btn-primary']);
    }

    public function hapusAction($id_produk)
    {
        $prod = Produk::findFirstByid_produk($id_produk);
        unlink($prod->foto_produk);
        $success = $prod->delete();
        $this->view->success = $success;

        if ($success)
         {
            echo "Produk berhasil dihapus. <br>";
        }
        // echo 'Produk berhasil dihapus.<br>';
        echo $this->tag->linkTo(['/produk/list', 'List Produk', 'class' => 'btn btn-primary']);
    }
    public function updateAction($id_produk)
    {
        $valid = new ProdukValidation();
        $message = $valid->validate($_POST);
        if(!count($message))
        {
            $prod = Produk::findFirstByid_produk($id_produk);
            $prod->assign(
                $this->request->getPost(),
                [
                    'nama_produk',
                    'brand_produk',
                    'deskripsi_produk',
                    'harga_produk',
                    'status_produk',
                ]
            );
            if($this->request->hasFiles())
            {
                $img = $this->request->getUploadedFiles()[0];
                $path = 'img/'.$img->getName();
                unlink($prod->foto_produk);
                $prod->foto_produk = $path;
                $img->moveTo($path);
            }
            // Store and check for errors
            $success = $prod->save();
            $this->view->success = $success;

            if ($success) {
                echo "Produk berhasil diupdate. <br>";
            } 
            echo $this->tag->linkTo(['/produk/list', 'List Produk', 'class' => 'btn btn-primary']);

        }
        else
        {
            foreach ($message as $msg) 
            {
                $this->flashSession->error($msg->getMessage());
            }
            echo $this->tag->linkTo(['/menu', 'Menu', 'class' => 'btn btn-primary']);

        }
    }
}