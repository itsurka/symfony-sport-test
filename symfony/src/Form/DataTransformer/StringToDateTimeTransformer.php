<?php


namespace App\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTimeTransformer implements DataTransformerInterface
{
    public function reverseTransform($string)
    {
        if (!$string) {
            return new \DateTime("now");
        }
        $date = new \DateTime((string)$string['date']);
        return $date;
    }

    public function transform($date)
    {
        if (!$date) {
            return null;
        }

        $string = date_parse(date_format($date, "Y-m-d H:i:s"));
        if (!$string) {
            throw new TransformationFailedException(sprintf(
                'Cant transform date to string!',
                $date
            ));
        }
        return $string;
    }
}