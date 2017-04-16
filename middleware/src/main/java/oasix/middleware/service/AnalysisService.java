package oasix.middleware.service;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Scanner;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Component;

import oasix.middleware.model.Data;
import oasix.middleware.model.Port;
import oasix.middleware.model.RequiredUpdate;
import oasix.middleware.model.ServerState;
import oasix.middleware.model.SystemInformation;
import oasix.middleware.model.Vulnerability;
import oasix.middleware.repository.ServerStateRepository;

@Component
public class AnalysisService {
	@Autowired
	ServerStateRepository serverStateRepository;

	@Autowired
	NiktoService niktoService;

	@Autowired
	VulnerabilityStatsService vulnerabilityStatsService;

	@Autowired
	RateStatsService rateStatsService;

	@Async
	public void analyse(String host, Date analysisTime, Data data) {
		rateStatsService.aggregate(host);

		ServerState serverState = createServerState(analysisTime, host, data);
		serverStateRepository.save(serverState);
	}

	private ServerState createServerState(Date analysisTime, String host, Data data) {
		ServerState serverState = new ServerState();
		serverState.setHost(host);
		serverState.setAnalysisDate(analysisTime);

		SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd-HH:mm:ss");
		String id = host + "-" + df.format(analysisTime);
		serverState.setId(id);

		SystemInformation systemInformation = new SystemInformation();
		systemInformation.setOperatingSystem("Linux");
		if(data != null){
			systemInformation.setVersion(data.getOsInfo());
		}else{
			systemInformation.setVersion("Ubuntu 16.02");
		}
		serverState.setSystemInformation(systemInformation);

		List<Port> openPorts = new ArrayList<>();
		if(data != null){
			openPorts.add(new Port(8080, "TCP", "open", "http"));
			openPorts.add(new Port(22, "TCP", "open", "ssh"));	
		}else{
//			String portdata = "dhclient    555    root    5u  IPv4   7645      0t0  UDP *:bootpc ";
//			Scanner scanner = new Scanner(portdata);
//			while (scanner.hasNextLine()) {
//			  String line = scanner.nextLine();
//			  if(line.startsWith("COMMAND")){
//				  line.split(" ");
//			  }
//			  // process the line
//			}
//			scanner.close();
			
			openPorts.add(new Port(8080, "TCP", "open", "http"));
			openPorts.add(new Port(22, "TCP", "open", "ssh"));
		}
		serverState.setOpenPorts(openPorts);

		List<Vulnerability> vulnerabilities = new ArrayList<>();

		vulnerabilities.addAll(niktoService.scan(host));

		// vulnerabilities.add(new Vulnerability("APPLICATION", "MAJEUR", "Sql
		// injection", "Lorem ipsum"));
		// vulnerabilities.add(new Vulnerability("APPLICATION", "MAJEUR",
		// "CSRF", "Lorem ipsum"));
		// vulnerabilities.add(new Vulnerability("ADMINISTRATION", "CRITIQUE",
		// "Root kit", "Lorem ipsum"));
		serverState.setVulnerabilities(vulnerabilities);
		serverState.setVulnerabilitiesCount(vulnerabilities.size());

		List<RequiredUpdate> requiredUpdates = new ArrayList<>();

		if (host.endsWith("113")) {
			requiredUpdates.add(new RequiredUpdate("bind9-host"));
			requiredUpdates.add(new RequiredUpdate("liblwres90"));
			requiredUpdates.add(new RequiredUpdate("libevent-2.0-5"));
			requiredUpdates.add(new RequiredUpdate("initramfs-tools-bin"));
			requiredUpdates.add(new RequiredUpdate("linux-headers-generic"));
			requiredUpdates.add(new RequiredUpdate("libgnutls-openssl27"));
			requiredUpdates.add(new RequiredUpdate("multiarch-support"));
			requiredUpdates.add(new RequiredUpdate("libdns100"));
			requiredUpdates.add(new RequiredUpdate("libisccfg90"));
			requiredUpdates.add(new RequiredUpdate("libbind9-90"));
			requiredUpdates.add(new RequiredUpdate("tcpdump"));
		}
		if (host.endsWith("85")) {
			requiredUpdates.add(new RequiredUpdate("libicu52"));
			requiredUpdates.add(new RequiredUpdate("libgc1c2"));
			requiredUpdates.add(new RequiredUpdate("linux-image-3.13.0-116-generic"));
		}
		if (host.endsWith("234")) {
			requiredUpdates.add(new RequiredUpdate("libcups2"));
			requiredUpdates.add(new RequiredUpdate("libfreetype6"));
			requiredUpdates.add(new RequiredUpdate("linux-image-virtual"));
			requiredUpdates.add(new RequiredUpdate("libc-dev-bin"));
			requiredUpdates.add(new RequiredUpdate("libapparmor1"));
			requiredUpdates.add(new RequiredUpdate("libc-bin"));
		}
		if (host.endsWith("122")) {
			requiredUpdates.add(new RequiredUpdate("libc6"));
			requiredUpdates.add(new RequiredUpdate("linux-virtual"));
			requiredUpdates.add(new RequiredUpdate("dnsutils"));
			requiredUpdates.add(new RequiredUpdate("linux-headers-virtual"));
			requiredUpdates.add(new RequiredUpdate("update-notifier-common"));
			requiredUpdates.add(new RequiredUpdate("initramfs-tools"));
			requiredUpdates.add(new RequiredUpdate("w3m"));
			requiredUpdates.add(new RequiredUpdate("eject"));
			requiredUpdates.add(new RequiredUpdate("libxml2"));
			requiredUpdates.add(new RequiredUpdate("linux-headers-3.13.0-116-generic"));
			requiredUpdates.add(new RequiredUpdate("libapparmor-perl"));
			requiredUpdates.add(new RequiredUpdate("libgnutls26"));
			requiredUpdates.add(new RequiredUpdate("makedev"));
			requiredUpdates.add(new RequiredUpdate("apparmor"));
			requiredUpdates.add(new RequiredUpdate("linux-libc-dev"));
			requiredUpdates.add(new RequiredUpdate("linux-headers-3.13.0-116"));
			requiredUpdates.add(new RequiredUpdate("libxml2-utils"));
			requiredUpdates.add(new RequiredUpdate("libisccc90"));
			requiredUpdates.add(new RequiredUpdate("libc6-dev"));
			requiredUpdates.add(new RequiredUpdate("libisc95"));
		}

		serverState.setRequiredUpdate(requiredUpdates);

		serverState.setRequiredUpdatesCount(requiredUpdates.size());
		serverState.setStatus("OK");
		serverState.setRate("" + evaluate(serverState));
		serverState.setTrend("UP");

		vulnerabilityStatsService.aggregate(serverState);

		return serverState;
	}

	private Integer evaluate(ServerState serverState) {
		Integer rate = 100 - (serverState.getVulnerabilitiesCount() * 10)
				- (int) (serverState.getRequiredUpdatesCount() * 0.5);
		if (rate > 0) {
			return rate;
		} else {
			return 0;
		}
	}

	public void cleanup() {
		serverStateRepository.delete(serverStateRepository.findAll());
	}
}
