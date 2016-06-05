Vagrant.require_version ">= 1.5"

# Check to determine whether we're on a windows or linux/os-x host,
# later on we use this to launch ansible in the supported way
# source: https://stackoverflow.com/questions/2108727/which-in-ruby-checking-if-program-exists-in-path-from-ruby
def which(cmd)
    exts = ENV['PATHEXT'] ? ENV['PATHEXT'].split(';') : ['']
    ENV['PATH'].split(File::PATH_SEPARATOR).each do |path|
        exts.each { |ext|
            exe = File.join(path, "#{cmd}#{ext}")
            return exe if File.executable? exe
        }
    end
    return nil
end

Vagrant.configure("2") do |config|

  config.vm.synced_folder "/Users/yemistikris/PhpstormProjects/sapar-project/audio-api/", "/var/www/audio-api/current", type: "nfs"
  config.vm.synced_folder "/Users/yemistikris/PhpstormProjects/sapar-project/radio/", "/var/www/radio/current", type: "nfs"
  config.vm.synced_folder "/Volumes/Extend/", "/Volumes/Extend", type: "nfs"
  config.vm.synced_folder "/Volumes/SSD_MAC/ddj", "/Volumes/SSD_MAC/ddj", type: "nfs"

    config.vm.provider :virtualbox do |v|
        v.name = "sapar.dev"
        v.customize [
            "modifyvm", :id,
            "--name", "sapar.dev",
            "--memory", 2048,
            "--natdnshostresolver1", "on",
            "--cpus", 2,
        ]
        #v.gui = true
    end

    config.vm.box = "ubuntu/trusty64"
    config.vm.box_check_update = true
    config.vm.network :private_network, ip: "10.0.0.5"
    config.ssh.forward_agent = true
    #config.ssh.private_key_path = "~/.ssh/id_rsa"
    #config.ssh.username = "yemistikris"

    # If ansible is in your path it will provision from your HOST machine
    # If ansible is not found in the path it will be instaled in the VM and provisioned from there
    if which('ansible-playbook')
        config.vm.provision "ansible" do |ansible|
            ansible.playbook = "devops/playbook.yml"
            #ansible.inventory_path = "devops/hosts/vagrant"
            ansible.limit = 'all'
        end
    else
        config.vm.provision :shell, path: "ansible/windows.sh", args: ["sapar.dev"]
    end

    #config.vm.synced_folder "./", "/vagrant", type: "nfs"
end
