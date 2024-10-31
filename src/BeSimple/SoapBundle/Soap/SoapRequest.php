<?php
/*
 * This file is part of the BeSimpleSoapBundle.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapBundle\Soap;

use BeSimple\SoapBundle\Util\Collection;
use Symfony\Component\HttpFoundation\Request;
use ZBateson\MailMimeParser\MailMimeParser;
use ZBateson\MailMimeParser\Header\HeaderConsts;

/**
 * SoapRequest.
 *
 * @author Christian Kerl <christian-kerl@web.de>
 */
class SoapRequest extends Request
{
    /**
     * @var string
     */
    protected $soapMessage;

    /**
     * @var string
     */
    protected $soapAction;

    /**
     * @var \BeSimple\SoapBundle\Util\Collection
     */
    protected $soapHeaders;

    /**
     * @var \BeSimple\SoapBundle\Util\Collection
     */
    protected $soapAttachments;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return SoapRequest
     */
    public static function createFromHttpRequest(Request $request)
    {
        return new static($request->query->all(), $request->request->all(), $request->attributes->all(), $request->cookies->all(), $request->files->all(), $request->server->all(), $request->content);
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param string|resource|null $content    The raw body data
     */
    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): void
    {
        parent::initialize($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->soapMessage     = null;
        $this->soapHeaders     = new Collection('getName', 'BeSimple\SoapBundle\Soap\SoapHeader');
        $this->soapAttachments = new Collection('getId', 'BeSimple\SoapBundle\Soap\SoapAttachment');

        $this->setRequestFormat('soap');
    }

    /**
     * Gets the XML string of the SOAP message.
     *
     * @return string
     */
    public function getSoapMessage()
    {
        if(null === $this->soapMessage) {
            $this->soapMessage = $this->initializeSoapMessage();
        }

        return $this->soapMessage;
    }

    public function getSoapHeaders()
    {
        return $this->soapHeaders;
    }

    public function getSoapAttachments()
    {
        return $this->soapAttachments;
    }

    protected function initializeSoapMessage()
    {
        if($this->server->has('CONTENT_TYPE')) {
            $type = $this->splitContentTypeHeader($this->server->get('CONTENT_TYPE'));

            switch($type['_type']) {
                case 'multipart/related':
                    if($type['type'] == 'application/xop+xml') {
                        return $this->initializeMtomSoapMessage($type, $this->getContent());
                    } else {
                        //log error
                    }
                    break;
                case 'application/soap+xml':
                    // goto fallback
                    break;
                default:
                    // log error
                    break;
            }
        }

        // fallback
        return $this->getContent();
    }

    protected function initializeMtomSoapMessage(array $contentTypeHeader, $content)
    {
        if(!isset($contentTypeHeader['start']) || !isset($contentTypeHeader['start-info']) || !isset($contentTypeHeader['boundary'])) {
            throw new \InvalidArgumentException();
        }

        $fullMessage = HeaderConsts::CONTENT_TYPE . ': ' . $this->server->get('CONTENT_TYPE') . "\r\n"
            . HeaderConsts::MIME_VERSION . ": 1.0\r\n"
            . "\r\n$content";

        $mailParser = new MailMimeParser();
        $message = $mailParser->parse($fullMessage, false);
        $mimeParts = $message->getAllParts();

        $soapMimePartId = trim($contentTypeHeader['start'], '<>');
        $soapMimePartType = $contentTypeHeader['start-info'];

        array_shift($mimeParts);
        $rootPart = array_shift($mimeParts);
        $rootPartType = $rootPart->getContentType();

        // TODO: add more checks to achieve full compatibility to MTOM spec
        // http://www.w3.org/TR/soap12-mtom/
        if($rootPart->getContentId() != $soapMimePartId || ($rootPartType != 'application/xop+xml' && $rootPartType != $soapMimePartType)) {
            throw new \InvalidArgumentException();
        }

        foreach($mimeParts as $mimePart) {
            $this->soapAttachments->add(new SoapAttachment(
                $mimePart->getContentId(),
                $mimePart->getContentType(),
                // handle content decoding / prevent encoding
                $mimePart->getContent()
            ));
        }

        // handle content decoding / prevent encoding
        return $rootPart->getContent();
    }

    protected function splitContentTypeHeader($header)
    {
        $result = array();
        $parts = explode(';', strtolower($header));

        $result['_type'] = array_shift($parts);

        foreach($parts as $part) {
            list($key, $value) = explode('=', trim($part), 2);

            $result[$key] = trim($value, '"');
        }

        return $result;
    }
}
