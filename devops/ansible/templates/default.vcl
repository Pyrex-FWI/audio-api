vcl 4.0;

# This is a basic VCL configuration file for varnish.  See the vcl(7)
# man page for details on VCL syntax and semantics.
#
# Default backend definition.  Set this to point to your content
# server.
backend dev {
  .host = "{{ dev_host }}";
  .port = "{{ varnish_default_backend_port }}";
}
backend test {
  .host = "{{ test_host }}";
  .port = "{{ varnish_default_backend_port }}";
}

sub vcl_recv {
   if (req.http.host == "{{dev_host}}") {
       #You will need the following line only if your backend has multiple virtual host names
       #set req.http.host = "example1.com";
       set req.backend_hint = dev;
   }
   if (req.http.host == "{{test_host}}") {
       #You will need the following line only if your backend has multiple virtual host names
       #set req.http.host = "example2.com";
       set req.backend_hint = test;
   }
}