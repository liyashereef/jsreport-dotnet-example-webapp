<?php

namespace Modules\Uniform\Repositories;

use App\Helpers\S3HelperService;
use Modules\Uniform\Models\UniformProduct;
use Modules\Uniform\Models\UniformProductImage;
use Modules\Uniform\Models\UniformProductVariant;


class UniformProductRepository
{
    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new UniformProductRepository instance.
     *
     * @param  Modules\Uniform\Models\UniformProduct $uniformProduct
     */
    public function __construct(UniformProduct $uniformProduct,UniformProductVariant $uniformProductVariant,
    UniformProductImage $uniformProductImage,S3HelperService $s3HelperService)
    {
        $this->model = $uniformProduct;
        $this->uniformProductVariant = $uniformProductVariant;
        $this->uniformProductImage = $uniformProductImage;
        $this->s3HelperService = $s3HelperService;
    }

    /**
     * Get Experience  lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->with(['images'])->get();
    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getImagePath($path)
    {
        $imagePath = $this->model->where('image_path',$path)->first();
        if(!empty($imagePath))
        {
            return false;
        }else{
            return true;
        }
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data)
    {
        return $this->model->updateOrCreate(array('id' => $data['id']), $data);
    }

    public function saveProductVariantMapping($data, $id)
    {
        $productId = $id;
        $productVariants = $data['variant_name'];
        UniformProductVariant::where('uniform_product_id', $productId)->delete();
            foreach ($productVariants as $k => $variantName) {
                UniformProductVariant::updateOrCreate([
                    'uniform_product_id' => $productId,
                    'variant_name' => $variantName,
                ]);
            }
    }

    public function saveProductImageMapping($data, $id)
    {
        $productId = $id;
        $productImages = $data['uploadedS3AttachedFileName'];
        UniformProductImage::where('uniform_product_id', $productId)->delete();
            foreach ($productImages as $k => $images) {
                $path = substr($images, 5);
                $this->s3HelperService->setPersistent($images,null);
                UniformProductImage::updateOrCreate([
                    'uniform_product_id' => $productId,
                    'image_path' =>$path,
                ]);
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        $delete_products = $this->model->destroy($id);
        $varients = $this->uniformProductVariant->where('uniform_product_id', $id)->get();
        if($varients){
            $delete_varients = $this->uniformProductVariant->where('uniform_product_id', $id)->delete();
        }
        $fileDetails =$this->uniformProductImage->where('uniform_product_id', $id)->get();
        if($fileDetails){
            $deleteFile = $this->uniformProductImage->where('uniform_product_id', $id)->delete();
            $s3ImagePath = $this->uniformProductImage->where('uniform_product_id', $id)->pluck('image_path');
            if(count($s3ImagePath) > 0){
                foreach ($s3ImagePath as $key => $each_list) {
                    S3HelperService::trashFile("awsS3Bucket", $each_list);
                }
            }
        }


    }

    /**
     * Get Experience lookup list
     *
     * @param empty
     * @return array
     */
    public function getUniformData()
    {
        $result = $this->model->with(['variants','images','taxMasterLog'])->orderBy('name')->get();
        return $this->prepareDataForUniformDetailList($result);
    }

    /**
     * Prepare datatable elements as array.
     * @param  $result
     * @return array
     */
    public function prepareDataForUniformDetailList($result)
    {
        $datatable_rows = array();
        foreach ($result as $key => $each_list) {
            $each_row["id"] = isset($each_list->id) ? $each_list->id : "";
            $each_row["name"] = isset($each_list->name) ? $each_list->name : "";
            $each_row["selling_price"] = isset($each_list->selling_price) ? $each_list->selling_price : "--";
            $each_row["tax_rate"] = (float) $each_list->taxMasterLog->tax_percentage;
            $each_row["price_with_tax"] =  $each_list->selling_price+(($each_list->taxMasterLog->tax_percentage * $each_list->selling_price)/100);
            $each_row["tax_amount"] = ($each_row["price_with_tax"] - $each_row["selling_price"]);

            $each_row["image"] = isset($each_list->image_path) ? $each_list->image_path : "--";
            $each_row_size = array();
            $each_row_image = array();
            if(!empty($each_list->variants))
            {
                foreach ($each_list->variants as $variantkey => $sizes) {
                    $each_row_size[$variantkey]['size_id'] = $sizes->id;
                    $each_row_size[$variantkey]['size'] = $sizes->variant_name;
                }
            }
            if(!empty($each_list->images))
            {
                foreach ($each_list->images as $imagekey => $image) {
                    $s3UrlPath = $this->s3HelperService->getPresignedUrl(null,$image->image_path);
                    $each_row_image[$imagekey]['image'] = $s3UrlPath ;
                }
            }
            $each_row['sizeArr'] = $each_row_size;
            $each_row['imageArr'] = $each_row_image;
            array_push($datatable_rows, $each_row);
        }
        return $datatable_rows;
    }

    public function deleteAttachment($id)
    {
        $fileDetails =$this->uniformProductImage->find($id);
        $fileloc = $fileDetails->image_path;
        S3HelperService::trashFile("awsS3Bucket", $fileloc);
        $deleteFile = $this->uniformProductImage->destroy($id);
        return $deleteFile;
    }

    public function getProduct($id)
    {
        return $this->model->where('id',$id)->first();
    }

}
