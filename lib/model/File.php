<?php



/**
 * Skeleton subclass for representing a row from the 'file' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.5.6 on:
 *
 * Mon Oct 24 09:36:19 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class File extends BaseFile {
  public function __toString()
  {
    return $this->getFilename();
  }
} // File
