<?php

class WXRDocument extends DOMDocument {

	/**
	 * WXR construction. Initialize a WXR object.
	 */
	public function WXRDocument() {
		parent::__construct( '1.0', 'UTF-8' );

		// create the rss element
		$this->createRSSElement();

		// create the channel element
		$this->createChannelElement();
	}

	/**
	 * Add the necessaries namespaces to the XML.
	 */
	function createRSSElement() {
		$rss = $this->createElement( 'rss' );

		$rss->setAttribute( 'version', '2.0' );

		$attribute = $this->createAttribute( 'xmlns:excerpt');
		$node = $this->createTextNode( 'http://wordpress.org/export/1.0/excerpt/' );
		$rss->appendChild( $attribute )->appendChild( $node );

		$attribute = $this->createAttribute( 'xmlns:content' );
		$node = $this->createTextNode( 'http://purl.org/rss/1.0/modules/content/' );
		$rss->appendChild( $attribute )->appendChild( $node );

		$attribute = $this->createAttribute( 'xmlns:wfw' );
		$node = $this->createTextNode( 'http://wellformedweb.org/CommentAPI/' );
		$rss->appendChild( $attribute )->appendChild( $node );

		$attribute = $this->createAttribute('xmlns:dc');
		$node = $this->createTextNode('http://purl.org/dc/elements/1.1/');
		$rss->appendChild( $attribute )->appendChild( $node );

		$attribute = $this->createAttribute('xmlns:wp');
		$node = $this->createTextNode('http://wordpress.org/export/1.0/');
		$rss->appendChild( $attribute )->appendChild( $node );

		$this->appendChild( $rss );
	}
	
	/**
	 * Create the channel elements with defaults values.
	 */
	function createChannelElement() {
		$channel = $this->createElement( 'channel' );
		
		// TODO Implement with real values
		$channel->appendChild( $this->createElement( 'title', 'Test title' ) );
		$channel->appendChild( $this->createElement( 'link', 'http://www.wordpress.org/' ) );
		$channel->appendChild( $this->createElement( 'description', 'Put here the description.' ) );
		$channel->appendChild( $this->createElement( 'pubDate', date( 'r' ) ) );
		$channel->appendChild( $this->createElement( 'generator', 'http://www.wordpress.org/' ) );
		$channel->appendChild( $this->createElement( 'language', 'pt-BR' ) );
		$channel->appendChild( $this->createElement( 'wp:wxr_version', '1.0' ) );
		$channel->appendChild( $this->createElement( 'wp:base_site_url', 'http://www.wordpress.org/' ) );
		$channel->appendChild( $this->createElement( 'wp:base_blog_url', 'http://www.wordpress.org/' ) );
		
		$this->getElementsByTagName( 'rss' )->item(0)->appendChild( $channel );
	}
	
	/**
	 * Get the channel element.
	 * 
	 * @return DOMNode The channel Element
	 */
	function getChannel() {
		return $this->getElementsByTagName( 'channel' )->item(0);
	}
	
	/**
	 * Add a category element in the document.
	 * 
	 * @param string $slug The slug of the category.
	 * @param string $name The name of the category.
	 * @param string $parent The parent of the category.
	 */
	function addCategory( $slug, $name, $parent = '' ) {
		$category = $this->createElement( 
				'wp:category', 
				array(
					$this->createElement( 'wp:category_nicename', $slug ),
					$this->createElement( 'wp:category_parent', $parent ),
					$this->createElement( 'wp:cat_name', $name, TRUE ),
				) 
			);
		
		$this->getChannel()->appendChild( $category );
	}
	
	/**
	 * Add a tag element in the document.
	 * 
	 * @param string $slug The slug of the tag.
	 * @param string $name The name of the tag.
	 */
	function addTag( $slug, $name ) {				
		$tag = $this->createElement( 
				'wp:tag', 
				array(
					$this->createElement( 'wp:tag_slug', $slug ),
					$this->createElement( 'wp:cat_name', $name, TRUE ),
				) 
			);

		$this->getChannel()->appendChild( $tag );
	}
	
	/**
	 * Add a term element in the document.
	 * 
	 * @param string $slug The slug of the tag.
	 * @param string $name The name of the tag.
	 */
	function addTerm( $taxonomy, $slug, $name, $parent = '' ) {				
		$term = $this->createElement( 
				'wp:term', 
				array(
					$this->createElement( 'wp:term_taxonomy', $taxonomy ),
					$this->createElement( 'wp:term_slug', $slug ),
					$this->createElement( 'wp:term_parent', $parent ),
					$this->createElement( 'wp:term_name', $name, TRUE ),
				) 
			);

		$this->getChannel()->appendChild( $term );
	}
	
	/**
	 * Add a cloud element in the document. Specification of the element in http://rsscloud.org/ 
	 * 
	 * @param array $args An array if domain, port, path, registerProcedure and protocol keys.
	 */
	function addCloud( $args = array() ) {		
		$args = $this->extend( array(
				'domain' => '',
				'port' => '',
				'path' => '',
				'registerProcedure' => '',
				'protocol' => '',
		), $args);
		
		$cloud = $this->createElement( 'cloud', '', FALSE, $args );
		
		$this->getChannel()->appendChild( $cloud );
	}
	
	/**
	 * Add a term element in the document.
	 * 
	 * @param string $url The url of the image.
	 * @param string $title The title of the image.
	 * @param string $link The link of the image.
	 */
	function addImage( $url, $title, $link ) {
		$image = $this->createElement(
				'image',
				array(
						$this->createElement( 'url', $url ),
						$this->createElement( 'title', $title ),
						$this->createElement( 'link', $link ),
				)
		);
		
		$this->getChannel()->appendChild( $image );
	}
	
	/**
	 * Add a atom:link element in the document. 
	 * 
	 * @param array $args An array if rel, type keys title keys.
	 */
	function addLink( $args = array() ) {
		 $args = $this->extend(array(
				'rel' => '',
				'type' => '',
				'href' => '',
				'title' => '',
		), $args);
		
		$link = $this->createElement( 'atom:link', '', FALSE, $args );
		
		$this->getChannel()->appendChild( $link );
	}
	
	/**
	 * Add a item element. Specification in http://feed2.w3.org/docs/rss2.html#hrelementsOfLtitemgt.
	 * 
	 * TODO Support GMT date
	 * 
	 * @param array $elements The elements values.
	 * @param array $wp_post_meta Metadata for the post (optional)
	 * @param array $comments Comments of the post (optional)
	 */
	function addItem( $elements, $wp_post_meta = array(), $comments = array() ) {
		
		// transform dates for the correct pattern
		if( isset( $elements['pubDate'] ) && $elements['pubDate'] ) {
			$elements['pubDate'] = date( 'r', strtotime( $elements['pubDate'] ) );
		}
		if( isset( $elements['wp:post_date'] ) && $elements['wp:post_date'] ) {
			$elements['wp:post_date'] = date( 'Y-m-d H:i:s', strtotime( $elements['wp:post_date'] ) );
		}
		if( isset( $elements['wp:post_date_gmt'] ) && $elements['wp:post_date_gmt'] ) {
			$elements['wp:post_date_gmt'] = date( 'Y-m-d H:i:s', strtotime( $elements['wp:post_date_gmt'] ) );
		}
		
		$item_elements = $this->extend(array(
				'title' => '',
				'link' => '',
				'description' => '',
				'source' => '',
				'enclosure' => '',
				'category' => '',
				'pubDate' => '',
				'guid' => '',
				'dc:author' => '',
				'content:encoded' => '',
				'excerpt:encoded' => '',
				'wp:post_id' => '',
				'wp:post_date' => '',
				'wp:post_date_gmt' => '',
				'wp:comment_status' => 'closed',
				'wp:ping_status' => 'closed',
				'wp:post_name' => '',
				'wp:status' => 'publish',
				'wp:post_parent' => 0,
				'wp:menu_order' => 0,
				'wp:post_type' => 'post',
				'wp:post_password' => '',
				'wp:is_sticky' => 0,
			), 
			$elements,
			array('wp:attachment_url')
		);
		
		$comments_default = array(
				'wp:comment_id' => '',
				'wp:comment_author' => '',
				'wp:comment_author_email' => '',
				'wp:comment_author_url' => '',
				'wp:comment_author_IP' => '',
				'wp:comment_date' => '',
				'wp:comment_date_gmt' => '',
				'wp:comment_content' => '',
				'wp:comment_approved' => 1,
				'wp:comment_type' => 'comment',
				'wp:comment_parent' => 0,
				'wp:comment_user_id' => 0,
		);
		
		$elements = array();
		foreach( $item_elements as $elem_name => $elem_value ) {
			switch( $elem_name ) {
				case 'content:encoded' :
				case 'excerpt:encoded' :
				case 'dc:author' :
					$cdata = TRUE;
					break;
				default:
					$cdata = FALSE;
					break;
			}
			
			$elements[] = $this->createElement( $elem_name, $elem_value, $cdata );
		}
		
		// add post meta values
		foreach( $wp_post_meta as $meta_key => $meta_value ) {
			$elements[] = $this->createElement( 'wp:postmeta', array(
					$this->createElement( 'wp:meta_key', $meta_key ),
					$this->createElement( 'wp:meta_value', $meta_value, TRUE ),
			) );
		}
		
		// add comments
		foreach( $comments as $comment ) {
			
			$comment_elements = $this->extend(array(
					'wp:comment_id' => '',
					'wp:comment_author' => '',
					'wp:comment_author_email' => '',
					'wp:comment_author_url' => '',
					'wp:comment_author_IP' => '',
					'wp:comment_date' => '',
					'wp:comment_date_gmt' => '',
					'wp:comment_content' => '',
					'wp:comment_approved' => 1,
					'wp:comment_type' => 'comment',
					'wp:comment_parent' => 0,
					'wp:comment_user_id' => 0,
			), $comment );
			
			$comment_elems = array();
			foreach ( $comment_elements as $comment_elem_name => $comment_elem_value ) {
				$comment_elems[] = $this->createElement( $comment_elem_name, $comment_elem_value );
			}
			
			$elements[] = $this->createElement( 'wp:comment', $comment_elems );
		}

		return $this->createElement( 'item', $elements );
	}
	
	/**
	 * Create a element and return it.
	 * 
	 * @param string $name The name of the element.
	 * @param string|array $content The content of the element. Can be a string or an array of elements.
	 * @param bool $cdata True, if content is string and the content must be in a CDATA element.
	 * @param array $attributes Array of attributes of the element.
	 * @return DOMElement The element created.
	 */
	function createElement( $name, $content = '', $cdata = FALSE, $attributes = array() ) {

		// create the element
		$element = ( is_array( $content ) || $cdata ) ?  
						$this->createElement( $name ) : 
						// https://bugs.php.net/bug.php?id=36795
						parent::createElement( $name, htmlspecialchars( $content ) ); 
		
		if( is_array( $attributes ) ) foreach ( $attributes as $name => $value ) {
			// https://bugs.php.net/bug.php?id=36795
			$element->setAttribute( $name, htmlspecialchars( $value ) );
		}
		
		if( is_array( $content ) ) {
			
			// append child elements
			foreach( $content as $child ) {
				$element->appendChild( $child );
			}
		
		} elseif( $cdata ) {
			
			$data = $this->createCDATASection( $content );
			$element->appendChild( $data );
		}

		return $element;
	}
	
	/**
	 * Insert a child into the channel node.
	 * 
	 * @param DOMElement $element
	 */
	function insertChannelChild( $element ) {
		$node = $this->importNode( $element, true );
		$this->getChannel()->appendChild( $node );
	}
	
	/**
	 * Get the doc size in bytes.
	 * 
	 * @return number The dic size in KB.
	 */
	function getDocSize() {
		return strlen( $this->saveXML() );
	}

	/**
	 * Clean and put default values in an array
	 *
	 * @param array $defaults Default values.
	 * @param array $args The array to be processed.
	 * @param array $optionals_keys Optional key to add in the array. (otptional)
	 * @return array The array with permitted values
	 */
	function extend( $defaults, $args, $optionals_keys = array() ) {
		// add optionals values if have
		$optionals = array();
		foreach($optionals_keys as $key) {
			if( array_key_exists( $key, $args ) ) {
				$optionals[$key] = $args[$key];
			}
		}
		// get only permitted values
		$default_keys = array_flip( array_keys( $defaults ) );
		$filter = array_intersect_key( $args, $default_keys );
		
		// return the merge array with all permitted values
		return array_merge( $defaults, $filter, $optionals );
	}
}
