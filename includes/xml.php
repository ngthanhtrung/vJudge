<?php
	defined('_SECURITY') or die("Access denied.");
	
	/**
	 * Parses an XML document into an object structure
	 *
	 * @var $parser
	 *   The XML parser
	 * @var $xml
	 *   The XML document
	 * @var $document
	 *   Document tag
	 * @var $stack
	 *   Current object depth
	 * @var $cleanTagNames
	 *   Whether or not to replace dashes and colons in tag
	 *   names with underscores.
	 */
	class xml {
		var $parser;
		var $xml;
		var $document;
		var $stack;
		var $cleanTagNames;
		
		/**
		 * Initiates and runs PHP's XML parser
		 * @param $xml
		 *   The path of the XML document
		 * @param $cleanTagNames
		 *   Set whether or not to clean tag names
		 */
		 function parse($xml = '', $cleanTagNames = true) {
			$this->xml = $xml;
			$this->stack = array();
			$this->cleanTagNames = $cleanTagNames;
			
			// Create the parser resource
			$this->parser = xml_parser_create();
			
			// Set the handlers
			xml_set_object($this->parser, $this);
			xml_set_element_handler($this->parser, 'startElement', 'endElement');
			xml_set_character_data_handler($this->parser, 'characterData');
			
			// Error handling
			if (!xml_parse($this->parser, $this->xml))
			$this->handleError(xml_get_error_code($this->parser), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser));
			
			// Free the parser
			xml_parser_free($this->parser);
		}
		
		/**
		 * Handles an XML parsing error
		 *
		 * @param $code
		 *   XML error Code
		 * @param $line
		 *   Line on which the error happened
		 * @param $col
		 *   Column on which the error happened
		 */
		 function handleError($code, $line, $col) {
			die('XML Parsing Error at ' . $line . ':' . $col . '. Error ' . $code . ': ' . xml_error_string($code));
		}
		
		/**
		 * Gets the XML output of the PHP structure within $this->document
		 *
		 * @return
		 *   XML document
		 */
		function generateXML() {
			return $this->document->getXML();
		}
		
		/**
		 * Gets the reference to the current direct parent
		 *
		 * @return
		 *   Current location in the stack
		 */
		function getStackLocation() {
			// Returns the reference to the current direct parent
			return end($this->stack);
		}
		
		/**
		 * Handler function for the start of a tag
		 *
		 * @param $parser
		 *   XML parser
		 * @param $name
		 *   Name of the tag
		 * @param $attrs
		 *   Attributes of the tag
		 */
		function startElement($parser, $name, $attrs = array()) {
			// Make the name of the tag lower case
			$name = strtolower($name);
			
			// Check to see if tag is root-level
			if (count($this->stack) == 0) {
				// If so, set the document as the current tag
				$this->document = new XMLTag($name, $attrs);
				
				// And start out the stack with the document tag
				$this->stack = array(&$this->document);
			}
			// If it isn't root level, use the stack to find the parent
			else {
				// Get the reference to the current direct parent
				$parent = $this->GetStackLocation();
				
				$parent->addChild($name, $attrs, count($this->stack), $this->cleanTagNames);
				
				// If the cleanTagName feature is on, clean the tag names
				if ($this->cleanTagNames)
					$name = str_replace(array(':', '-'), '_', $name);
					
				// Update the stack
				$this->stack[] = end($parent->$name);        
			}
		}
		
		/**
		 * Handler function for the end of a tag
		 *
		 * @param $parser
		 *   XML parser
		 * @param $name
		 *   Name of the tag
		 */
		function endElement($parser, $name) {
			// Update stack by removing the end value from it as the parent
			array_pop($this->stack);
		}
		
		/**
		 * Handler function for the character data within a tag
		 *
		 * @param $parser
		 *   XML parser
		 * @param $data
		 *   Data within a tag
		 */
		 function characterData($parser, $data) {
			// Get the reference to the current parent object
			$tag = $this->getStackLocation();
			
			// Assign data to it
			$tag->tagData .= trim($data);
		}
	}
	
	/**
	 * This object stores all of the direct children of itself in the $children array. They are also stored by
	 * type as arrays. So, if, for example, this tag had 2 <font> tags as children, there would be a class member
	 * called $font created as an array. $font[0] would be the first font tag, and $font[1] would be the second.
	 * 
	 * To loop through all of the direct children of this object, the $children member should be used.
	 *
	 * To loop through all of the direct children of a specific tag for this object, it is probably easier 
	 * to use the arrays of the specific tag names, as explained above.
	 *
	 * @var $tagAttrs
	 *   Array with the attributes of this XML tag
	 * @var $tagName
	 *   Name of the tag
	 * @var $tagData
	 *   The data the tag contains 
	 *   So, if the tag doesn't contain child tags, and just contains a string, it would go here
	 * @var $tagChildren
	 *   Array of references to the objects of all direct children of this XML object
	 * @var $tagParents
	 *   The number of parents this XML object has (number of levels from this tag to the root tag)
	 *   Used presently only to set the number of tabs when outputting XML
	 */
	class XMLTag {
		var $tagAttrs;
		var $tagName;
		var $tagData;
		var $tagChildren;
		var $tagParents;
		
		/**
		 * Constructor, sets up all the default values
		 *
		 * @param $name
		 *   Name of the tag
		 * @param $attrs
		 *   All attributes
		 * @param $parents
		 *   The number of parents
		 */
		function XMLTag($name, $attrs = array(), $parents = 0) {
			// Make the keys of the attr array lower case, and store the value
			$this->tagAttrs = array_change_key_case($attrs, CASE_LOWER);
			
			// Make the name lower case and store the value
			$this->tagName = strtolower($name);
			
			// Set the number of parents
			$this->tagParents = $parents;
			
			// Set the types for children and data
			$this->tagChildren = array();
			$this->tagData = '';
		}
		
		/**
		 * Adds a direct child to this object
		 *
		 * @param $name
		 *   Name of the child
		 * @param $attrs
		 *   Attributes of the chid
		 * @param $parents
		 *   The number of parents of the child
		 * @param $cleanTagName
		 *   Set whether or not to clean tag names
		 */
		 function addChild($name, $attrs, $parents, $cleanTagName = true) {    
			// If the tag is a reserved name, output an error
			if (in_array($name, array('tagChildren', 'tagAttrs', 'tagParents', 'tagData', 'tagName')))
				die("You have used a reserved name as the name of an XML tag.<br />"
				. "\nPlease and rename the tag named \"" . $name . "\" to something other than a reserved name.");
							
			// Create the child object itself
			$child = new XMLTag($name, $attrs, $parents);
			
			// If the cleanTagName feature is on, replace colons and dashes with underscores
			if ($cleanTagName)
				$name = str_replace(array(':', '-'), '_', $name);
				
			// Toss up a notice if someone's trying to to use a colon or dash in a tag name
			elseif (strstr($name, ':') || strstr($name, '-'))
				die("Your tag named \"" . $name . "\" contains either a dash or a colon.<br />"
				. "\nNeither of these characters are friendly with PHP variable names, and, as such, you may have difficulty accessing them.");
				
			// If there is no array already set for the tag name being added, create an empty array for it
			if (!isset($this->$name))
				$this->$name = array();
				
			// Add the reference of it to the end of an array member named for the tag's name
			$this->{$name}[] = &$child;
			
			// Add the reference to the children array member
			$this->tagChildren[] = &$child;
			
			// Return a reference to this object for the stack
			return $this;
		}
		
		/**
		 * Returns the string of the XML document which would be generated from this object
		 * 
		 * This function works recursively, so it gets the XML of itself and all of its children, which
		 * in turn gets the XML of all their children, which in turn gets the XML of all their children,
		 * and so on. So, if you call GetXML from the document root object, it will return a string for 
		 * the XML of the entire document.
		 * 
		 * This function does not, however, return a DTD or an XML version/encoding tag. That should be
		 * handled by XMLParser::GetXML()
		 *
		 * @return
		 *   XML content
		 */
		 function getXML() {
			// Start a new line, indent by the number indicated in $this->parents, add a <, and add the name of the tag
			$out = "\n" . str_repeat("\t", $this->tagParents) . '<' . $this->tagName;
			
			// For each attribute, add attr="value"
			foreach ($this->tagAttrs as $attr => $value)
				$out .= ' ' . $attr . '="' . $value . '"';
				
			// If there are no children and it contains no data, end it off with a />
			if (empty($this->tagChildren) && empty($this->tagData))
				$out .= " />";
			// Otherwise...
			else {
				// If there are children
				if (!empty($this->tagChildren)) {
					// Close off the start tag
					$out .= '>';
					
					// For each child, call the GetXML function (this will ensure that all children are added recursively)
					foreach ($this->tagChildren as $child)
						$out .= $child->GetXML();
						
					// Add the newline and indentation to go along with the close tag
					$out .= "\n" . str_repeat("\t", $this->tagParents);
				}
				// If there is data, close off the start tag and add the data
				elseif (!empty($this->tagData))
					$out .= '>' . $this->tagData;
					
				// Add the end tag
				$out .= '</' . $this->tagName . '>';
			}
			
			// Return the final output
			return $out;
		}
		
		/**
		 * Deletes this tag's child with a name of $childName and an index of $childIndex
		 *
		 * @param $childName
		 *   Name of the child
		 * @param $childIndex
		 *   Index of the child in the array
		 */
		 function delete($childName, $childIndex = 0) {
			// Delete all of the children of that child
			$this->{$childName}[$childIndex]->DeleteChildren();
			
			// Destroy the child's value
			$this->{$childName}[$childIndex] = null;
			
			// Remove the child's name from the named array
			unset($this->{$childName}[$childIndex]);
			
			// Loop through the tagChildren array and remove any null
			// values left behind from the above operation
			for ($x = 0; $x < count($this->tagChildren); $x ++) {
				if (is_null($this->tagChildren[$x]))
					unset($this->tagChildren[$x]);
			}
		}
		
		/**
		 * Removes all of the children of this tag in both name and value
		 */
		 function deleteChildren() {
			// Loop through all child tags
			for ($x = 0; $x < count($this->tagChildren); $x ++) {
				// Do this recursively
				$this->tagChildren[$x]->DeleteChildren();
				
				// Delete the name and value
				$this->tagChildren[$x] = null;
				unset($this->tagChildren[$x]);
			}
		}
	}
?>