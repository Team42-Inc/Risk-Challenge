package oasix.middleware.repository;

import java.util.List;

import org.springframework.data.elasticsearch.repository.ElasticsearchCrudRepository;

import oasix.middleware.model.ServerState;
import oasix.middleware.model.VulnerabilityStats;

public interface ServerStateRepository extends ElasticsearchCrudRepository<ServerState, String> {
	List<ServerState> findByHost(String host);
}