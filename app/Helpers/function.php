<?php
if(!function_exists('getExtension'))
{
    function getExtension($ext)
    {
        if($ext=="plain")
        {
            return 'txt';
        }
        elseif($ext=="vnd.openxmlformats-officedocument.wordprocessingml.document")
        {
            return 'docx';
        }
        elseif($ext=="pdf")
        {
            return 'pdf';
        }
        elseif($ext=="msword")
        {
            return 'doc';
        }
        return $ext;
    }
}
if (!function_exists('validate_base64')) {
    function validate_base64($base64data, array $allowedMime,$size)
    {
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            list(, $base64data) = explode(';', $base64data);
            list(, $base64data) = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            return false;
        }

        // decoding and then reeconding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            return false;
        }

        $binaryData = base64_decode($base64data);

        // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
        $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
        file_put_contents($tmpFile, $binaryData);

        // guard Against Invalid MimeType
        $allowedMime = array_flatten($allowedMime);

        // no allowedMimeTypes, then any type would be ok
        if (empty($allowedMime)) {
            return true;
        }

        // Check the MimeTypes
            $validation = Illuminate\Support\Facades\Validator::make(
                ['file' => new Illuminate\Http\File($tmpFile)],
                ['file' => 'required|max:'.$size.'|mimes:' . implode(',', $allowedMime)]
            );
            if(!$validation->fails())
            {
                return true;
            }
            return false;
        }
    }
