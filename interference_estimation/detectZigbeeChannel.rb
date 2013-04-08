## global variables and properties
defProperty("SENSINGTIME",30,"The sensing duration (in second)")
usrpID=['81','89','65','69']
startTime=nil
threshold=-85
currentChannel = 21
###################################################
################### APP DEF #######################
defApplication('iriswrap', 'iriswrap') do |app|
   app.path = File.join(File.dirname(__FILE__), 'iriswrap_zb_omf.rb')
   app.appPackage = "usrpSE_ecdb.tar"
# some descriptions
   app.version(1, 0, 0)
   app.shortDescription = "irisapp"
   app.description = "run iris platform"

   app.defProperty('xmlfile', 'The configurationfile location', 'f', {:dynamic => false, :type => :string})
   app.defMeasurement('iriswrapmp') do |mp|
      mp.defMetric('hostname',:string)
      mp.defMetric('timestamp_us',:string)
      mp.defMetric('usrpid',:string)
      mp.defMetric('dummy',:long)
   end

end
###################################################
################### OEDL #######################
## USRP SE

defGroup('server3_1', "omf.ibbt.open.server3") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root_zb/usrp_n1_zb.xml')
        app.measure('iriswrapmp', :samples=>1)
  }
}

defGroup('server3_2', "omf.ibbt.open.server3") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root_zb/usrp_n2_zb.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server4_1', "omf.ibbt.open.server4") {|node|    
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root_zb/usrp_n3_zb.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server4_2', "omf.ibbt.open.server4") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root_zb/usrp_n4_zb.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server6_1', "omf.ibbt.open.server6") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root_zb/usrp_n5_zb.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server6_2', "omf.ibbt.open.server6") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root_zb/usrp_n6_zb.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

onEvent(:ALL_UP_AND_INSTALLED) do |event|

#  info "configuring interfaces for USRP sensing engines.."
  group("server3_1").exec("/omf_root/./setupusrp2.sh -i eth2 2")
  group("server3_2").exec("/omf_root/./setupusrp2.sh -i eth3 6")
  group("server4_1").exec("/omf_root/./setupusrp2.sh -i eth2 10")
  group("server4_2").exec("/omf_root/./setupusrp2.sh -i eth3 14")
  group("server6_1").exec("/omf_root/./setupusrp2.sh -i eth2 18")
  group("server6_2").exec("/omf_root/./setupusrp2.sh -i eth3 22")
#  info "start up sensing engines"
  allGroups.exec("killall irisv2")
  wait 1 
  allGroups.startApplications;
  wait property.SENSINGTIME

  Experiment.done
end
