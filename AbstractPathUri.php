<?php
namespace Poirot\PathUri;

use Poirot\Core\BuilderSetterTrait;
use Poirot\PathUri\Interfaces\iBasePathUri;

abstract class AbstractPathUri
    implements iBasePathUri
{
    use BuilderSetterTrait {
        setupFromArray as protected __fromArray;
    }

    private $__reseting;

    /**
     * Create a new URI object
     *
     * @param iBasePathUri|string|array $pathUri
     *
     * @throws \InvalidArgumentException
     */
    function __construct($pathUri = null)
    {
        if ($pathUri !== null)
            $this->from($pathUri);
    }

    /**
     * Set From Resource
     *
     * @param  iBasePathUri|string|array $pathUri
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function from($pathUri)
    {
        if (is_string($pathUri))
            $pathUri = $this->parse($pathUri);

        if (is_array($pathUri))
            $this->fromArray($pathUri);
        elseif (is_object($pathUri))
            $this->fromPathUri($pathUri);
        else
            throw new \InvalidArgumentException(sprintf(
                'PathUri must be instanceof iPathUri, Array or String, given: %s'
                , is_object($pathUri) ? get_class($pathUri) : gettype($pathUri)
            ));
    }

    /**
     * Build Object From PathUri
     *
     * - don't reset this object, so values merged with new one
     *
     * note: it take a instance of pathUri object
     *   same as base object
     *
     * @param iBasePathUri $path
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function fromPathUri(/*iPathAbstractUri*/ $path)
    {
        if (!is_object($path) || ! $path instanceof $this)
            throw new \InvalidArgumentException(sprintf(
                'PathUri must be instanceof %s, given: %s'
                , get_class($this)
                , is_object($path) ? get_class($path) : gettype($path)
            ));

        $this->fromArray($path->toArray());

        return $this;
    }

    /**
     * Build Object From Array
     *
     * @param array $arrPath
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    function fromArray(array $arrPath)
    {
        $this->__fromArray($arrPath);

        return $this;
    }

    /**
     * Reset parts
     *
     * @return $this
     */
    function reset()
    {
        $this->__reseting = true; // recursive fromArray call on reseting

        $arrCp = $this->toArray();
        foreach($arrCp as $key => &$val)
            $val = null;

        $this->fromArray($arrCp);

        $this->__reseting = false;

        return $this;
    }
}
 