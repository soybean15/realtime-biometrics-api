<?php

namespace App\Traits;

trait ImageTrait
{
    public function storeImage($path, $file)
    {
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '.' . $fileExtension;

        $file->move($path, $fileName);

        $this->image = $fileName;

        $this->save();
    }

    public function restoreImage($path, $file)
    {


        $this->unlinkImage();
        $this->storeImage($path, $file);
    }

    public function unlinkImage()
    {
        if ($this->getRawImageAttribute() != '' && $this->getRawImageAttribute() != null) {

            if ($file_old = $this->getRawImageAttribute()) {
                unlink($file_old);
            }
        }
    }

    public function getRawImageAttribute()
    {
        if ($this->attributes['image']) {
            $path = public_path('images/users/' . $this->attributes['image']);

            if (file_exists($path)) {
                return $path;
            }
            return null;
        } else {
            return null;
        }
    }
}
