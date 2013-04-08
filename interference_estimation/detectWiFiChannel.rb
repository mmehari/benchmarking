# Start input definition
defProperty('CHANNEL', 13, 'The WIFI channel measured by sensing engines')
defProperty('GAIN',50,'The gain used by sensing engines')
defProperty('maxhold',1,'The maxhold filter flag, 0 disable 1 enable ')
defProperty('nsamps',524288,'The number of samples as input for periodogram')
defProperty('tofile',0,'1 for storing the result locally, 20 for storing the result on omf database')
defProperty('outputinterval',5,'produce one output every XX input sample')
defProperty('SENSINGTIME',30,'The sensing duration (in second)')
# End input definition
###################################################
################### APP DEF #######################

defApplication('iriswrap', 'iriswrap') do |app|
   app.path = File.join(File.dirname(__FILE__), 'iriswrap.rb')
   app.appPackage = "usrpSE.tar"
# some descriptions
   app.version(1, 0, 0)
   app.shortDescription = "irisapp"
   app.description = "run iris platform"

   app.defProperty('xmlfile', 'The configurationfile location', 'f', {:dynamic => false, :type => :string})
   app.defMeasurement('iriswrapmp') do |mp|
      mp.defMetric('hostname',:string)
      mp.defMetric('timestamp_us',:string)
      mp.defMetric('usrpid',:string)
      mp.defMetric('psd',:long)
   end

end

###################################################
################### OEDL #######################
## USRP SE

defGroup('server3_1', "omf.ibbt.open.server3") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root/usrp_n1.xml')
        app.measure('iriswrapmp', :samples=>1)
  }
}

defGroup('server3_2', "omf.ibbt.open.server3") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root/usrp_n2.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server4_1', "omf.ibbt.open.server4") {|node|    
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root/usrp_n3.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server4_2', "omf.ibbt.open.server4") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root/usrp_n4.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server6_1', "omf.ibbt.open.server6") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root/usrp_n5.xml')
        app.measure('iriswrapmp', :samples=>1)
    }
}

defGroup('server6_2', "omf.ibbt.open.server6") {|node|
        node.addApplication("iriswrap") {|app|
        app.setProperty('xmlfile', '/omf_root/usrp_n6.xml')
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
  wait 1
#  info "configure channel, gain and some minor aspects of the sensing engine if necessary.."
  group("server3_1").exec("omf_root/./reCfg.sh -a omf_root/usrp_n1.xml #{property.CHANNEL} #{property.GAIN} #{property.nsamps} #{property.outputinterval} #{property.tofile} #{property.maxhold}")
  group("server3_2").exec("omf_root/./reCfg.sh -a omf_root/usrp_n2.xml #{property.CHANNEL} #{property.GAIN} #{property.nsamps} #{property.outputinterval} #{property.tofile} #{property.maxhold}")
  group("server4_1").exec("omf_root/./reCfg.sh -a omf_root/usrp_n3.xml #{property.CHANNEL} #{property.GAIN} #{property.nsamps} #{property.outputinterval} #{property.tofile} #{property.maxhold}")
  group("server4_2").exec("omf_root/./reCfg.sh -a omf_root/usrp_n4.xml #{property.CHANNEL} #{property.GAIN} #{property.nsamps} #{property.outputinterval} #{property.tofile} #{property.maxhold}")
  group("server6_1").exec("omf_root/./reCfg.sh -a omf_root/usrp_n5.xml #{property.CHANNEL} #{property.GAIN} #{property.nsamps} #{property.outputinterval} #{property.tofile} #{property.maxhold}")
  group("server6_2").exec("omf_root/./reCfg.sh -a omf_root/usrp_n6.xml #{property.CHANNEL} #{property.GAIN} #{property.nsamps} #{property.outputinterval} #{property.tofile} #{property.maxhold}")
  wait 5
  allGroups.exec("killall irisv2")
  wait 1 
  allGroups.startApplications;
 
  wait property.SENSINGTIME

  allGroups.stopApplications;
  Experiment.done
end



























































































