<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="YoutubeChannel")
     * @ORM\JoinColumn(name="youtube_channel_id", referencedColumnName="id")
     */
    private $youtubeChannel;

    public function __construct() {
        parent::__construct();
        // your own logic
    }


    /**
     * Set youtubeChannel
     *
     * @param \AppBundle\Entity\YoutubeChannel $youtubeChannel
     *
     * @return User
     */
    public function setYoutubeChannel(\AppBundle\Entity\YoutubeChannel $youtubeChannel = null)
    {
        $this->youtubeChannel = $youtubeChannel;

        return $this;
    }

    /**
     * Get youtubeChannel
     *
     * @return \AppBundle\Entity\YoutubeChannel
     */
    public function getYoutubeChannel()
    {
        return $this->youtubeChannel;
    }
}
