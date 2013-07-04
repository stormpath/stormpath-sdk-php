<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vganesh
 * Date: 7/2/13
 * Time: 11:57 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Resource;

class Directory
{

    private  $name;
    private  $description;
    private  $status;

    public  function getName()
    {

        return $this->name;
    }

    public  function setName($value)
    {
        $this->name = $value;
    }

    public  function getDescription()
    {

        return $this->description;
    }

    public  function setDescription($value)
    {
        $this->description = $value;
    }

    public  function getStatus()
    {

        return $this->status;
    }

    public  function setStatus($value)
    {
        $this->status = $value;
    }

    public  function configure($name,$description,$status)
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->setStatus($status);
    }

}