class AtlasClient
  require "net/https"
  require "uri"
  require 'json'
  require 'cgi'
  
  def initialize(params = {})
    @base_url = "https://test-api.atlasexpress.se/"
    @username = params[:api_id] 
    @password = params[:password]
  end

  def getInvoice(id)
    self.call("invoices/#{id}")
  end
  
  def sendInvoice(data)
    self.call("invoices", "POST", data)
  end

  def call(action, method = 'GET', data = '')
    @url = URI.parse(@base_url + action)
    client = Net::HTTP.new(@url.host, @url.port)
    client.use_ssl = true
    client.verify_mode = OpenSSL::SSL::VERIFY_NONE
    
    case method
      when "POST"
        request = Net::HTTP::Post.new(@url.request_uri)
        data = CGI::parse(data.to_params)
        request.set_form_data(data)
      when "GET"
        request = Net::HTTP::Get.new(@url.request_uri)
    end
    
    request.basic_auth(@username, @password)
    response = client.request(request)

    return JSON.parse(response.body)
  end
  
end

class Hash
  def to_params
    params = ''
    stack = []

    each do |k, v|
      if v.is_a?(Hash)
        stack << [k,v]
      else
        params << "#{k}=#{v}&"
      end
    end

    stack.each do |parent, hash|
      hash.each do |k, v|
        if v.is_a?(Hash)
          stack << ["#{parent}[#{k}]", v]
        else
          params << "#{parent}[#{k}]=#{v}&"
        end
      end
    end

    params.chop! # trailing &
    params
  end
end
