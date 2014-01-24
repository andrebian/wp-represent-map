<?php

class Upload
{
    
    /**
     *
     * @var string 
     */
    protected $basePath;
    
    /**
     *
     * @var array 
     */
    protected $file;
    
    /**
     *
     * @var array 
     */
    protected $allowedTypes = array();
    
    /**
     *
     * @var array 
     */
    protected $errors = array();
    
    
    /**
     * 
     * @param string $basePath
     */
    public function setBasePath( $basePath ) 
    {
        $this->basePath = $basePath;
    }
    
    /**
     * 
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    /**
     * 
     * @param string $type
     */
    public function appendAllowedType( $type )
    {
        array_push( $this->allowedTypes, $type );
    }
    
    /**
     * 
     * @return array
     */
    public function getAllowedTypes()
    {
        return $this->allowedTypes;
    }
    
    /**
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * 
     * @param array $file
     * @return \Upload
     */
    public function prepareUpload( $file )
    {
        $this->file = $file;
        
        return $this;
    }
    
    /**
     * 
     * @return \Upload
     */
    public function flush()
    {
        if ( 0 === $this->file['error'] ) {
            
            if ( !in_array($this->file['type'], $this->allowedTypes) ) {
                array_push( $this->errors, 'The uploaded file type is not allowed' );
                return $this;
            }
            
            $this->__moveReceivedFileToDestination();
            
        } else {
            array_push( $this->errors, 'The uploaded file has an error and is not possible to move to destination' );
        }
        
        return $this;
    }
    
    /**
     * 
     * @param string $file
     * @return \Upload
     */
    public function removeFile( $file )
    {
        try{
            unlink($this->basePath . '/' . $file);
        } catch (Exception $ex) {
            array_push($this->errors, 'Error on delete the file. ' . $ex->getMessage() . ' ' . $ex->getTrace());
        }
        
        return $this;
    }
    
    /**
     * 
     * @return \Upload
     */
    private function __moveReceivedFileToDestination()
    {
        try{
            move_uploaded_file($this->file['tmp_name'], $this->basePath . '/' . $this->file['name']);
        } catch (Exception $ex) {
            array_push( $this->errors, sprintf('Fail to upload the file. Info: %s', $ex->getMessage()) );
        }
        
        return $this;
    }
    
}
