require './atlas_client.rb'

# Setup your credentials
api_id = "4e97eaa20f9e972364116b74616"
password = 'daniel'


# Connect to the API
atlas = AtlasClient.new( api_id: api_id, password: password)

# Create a new invoice 
new_invoice = atlas.sendInvoice(
    invoice_date: '2011-11-18',
    due_days: 30,
    customer_no: 1,
    delivery_type: 'Letter',
    items: {
         0 => {title: 'Ostar', num: 2, price: 139}
    }    
) 

# Show some debug information about the newly created invoice
puts new_invoice

# Load back the same invoice from Atlas Express...
same_invoice = atlas.getInvoice(new_invoice['invoice_no'])
# ...and show some debug information
puts same_invoice
