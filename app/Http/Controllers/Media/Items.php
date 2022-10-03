<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ValidationError;
use App\Models\Item;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Models\ItemImage;
use Illuminate\Support\Facades\Storage;

class Items extends Controller
{
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
            'image'   => 'required|image|mimes:jpeg,png,jpg|max:30720',
            'pos_x'   => 'numeric',
            'pos_y'   => 'numeric',
            'scale'   => 'numeric',
        ]);

        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());

        $item = Item::find($request->get('item_id'));

        if (! $item)
            throw new NotFoundException(Item::class, $request->get('item_id'));

        $authUser = auth()->user()->userData;
        
        if ($authUser->cannot('update', $item))
            throw new ForbiddenException();

        $numOfImgs = $item->images()->where("is_cover", false)->count();
        if ($numOfImgs === 5)
            throw new ForbiddenException('You cannot upload more than 5 images per item');

        $imageUri = $request->file('image')->store('items/images', 'public');

        $img = \Image::make(storage_path('app/public/' . $imageUri));
        $img->save();

        $image = $item->addImage($imageUri, $request->get('pos_x'), $request->get('pos_y'), $request->get('scale'));
        
        return response()->json([
            'message' => 'Image uploaded',
            'image_uri' => $imageUri,
            'image_id' => $image->id,
        ]);
    }

    public function uploadCoverImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "item_id" => "required|integer",
            "image"   => "required|image|mimes:jpeg,jpg,png|max:30720"
        ]);

        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());

        $item = Item::find($request->get("item_id"));
        if (!$item)
            throw new NotFoundException(Item::class, $request->get('item_id'));

        $authUser = auth()->user()->userData;
        if ($authUser->cannot("update", $item))
            throw new ForbiddenException();

        $savedCoverImage = $item->coverImage;        
        if ($savedCoverImage)
            Storage::disk('public')->delete($savedCoverImage->uri);
            
        $newCoverUri = $request->file('image')->store('items/images', 'public');
        $newCover = $item->updateOrAddCoverImage($newCoverUri);
        
        return response()->json([
            "success"   => true,
            "image_uri" => $newCover->uri,
            "image_id"  => $newCover->id
        ]);
    }

    public function deleteImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image_id' => 'required|integer'
        ]);

        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());

        $image = ItemImage::find($request->get('image_id'));
        if (! $image)
            throw new NotFoundException(ItemImage::class, $request->get('image_id'));

        $authUser = auth()->user()->userData;
        if ($authUser->cannot('update', $image->item))
            throw new ForbiddenException();

        Storage::disk('public')->delete($image->uri);

        $image->delete();
        return response()->json([
            'success' => true,
            'message' => 'Image deleted'
        ]);
    }

    public function uploadVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
            'video'   => 'required|mimes:mp4|max:122880',
        ]);
        
        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());
        
        $item = Item::find($request->get('item_id'));
        if (! $item)
            throw new NotFoundException(Item::class, $request->get('item_id'));
        
        $authUser = auth()->user()->userData;
        if ($authUser->cannot('update', $item))
            throw new ForbiddenException();

        if ($item->video_uri)
            Storage::disk('public')->delete($item->video_uri); // delete old video
        
        $videoUri = $request->file('video')->store('items/videos', 'public');
        $item->video_uri = $videoUri;
        $item->save();
        
        return response()->json([
            'message' => 'Video uploaded',
            'video_uri' => $videoUri,
        ]);
    }

    public function deleteVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer'
        ]);
        
        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());
        
        $item = Item::find($request->get('item_id'));
        if (! $item)
            throw new NotFoundException(Item::class, $request->get('item_id'));
        
        $authUser = auth()->user()->userData;
        if ($authUser->cannot('update', $item))
            throw new ForbiddenException();
        
        if ($item->video_uri)
            Storage::disk('public')->delete($item->video_uri);
        
        $item->video_uri = null;
        $item->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Video deleted'
        ]);
    }
}
