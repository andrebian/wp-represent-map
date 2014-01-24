<?php

class RepresentMap
{
    protected $type = null;
    protected $args;
    
    
    /**
     * 
     * @param array $type
     */
    public function setType( $type ) 
    {
        if ( !empty($type) ) {
            $this->type = $type['type'];
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * 
     * @return type
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 
     * @return array
     */
    public function getArgs()
    {
        return $this->__parseArgs();
    }
    
    
    /**
     * 
     * @return array
     */
    private function __parseArgs()
    {
        if ( !empty($this->type) ) {
            $args = array(
                'tax_query' => array(
                    array(
                        'taxonomy' => 'represent_map_type',
                        'field' => 'name',
                        'terms' => $this->type
                    )
                )
            );
        } else {
            $args = array(
                'post_type' => 'represent_map'
            );
        }
        
        $this->args = $args;
        
        return $this->args;
    }
    
}