#Python class for Billogram REST API, author: Andreas Högström at Agigen http://agigen.se/
#encoding=utf-8
import json
from urllib import quote_plus
import urllib2
import base64

class BillogramClient(object):
    def __init__(self,host,apiId,apiPassword):
        self.host = host
        self.apiId = apiId
        self.apiPassword = apiPassword

    def __call(self,action, method='GET', data=''):
        '''Send a request to the given host.
           Raises exceptions on any kind of error.
           Returns the JSON-encoded response as real python objects.'''
        request = urllib2.Request(self.host+action)
        request.add_header('Authorization','Basic %s' % (base64.encodestring('%s:%s'%(self.apiId,self.apiPassword)).replace('\n','')))
        response = urllib2.urlopen(request,data=BillogramClient.parse(data) if data else None)
        return json.loads(response.read())

    def getInvoice(self,id):
        return self.__call('invoices/%s' % (id))

    def createInvoice(self,data):
        return self.__call('invoices','POST',data)

    @staticmethod
    def parse(data):
        '''Manually construct the http parameters since urllib.urlencode doesn't handle nested structures as we want.'''
        params = []
        for key,value in data.items():
            if key == 'items':
                for i,item in enumerate(value):
                    for itemKey,itemValue in item.items():
                        params.append('%s=%s'%(quote_plus('items[%d][%s]'%(i,str(itemKey))),quote_plus(str(itemValue))))
            else:
                params.append('%s=%s'%(quote_plus(str(key)),quote_plus(str(value))))
        return '&'.join(params)