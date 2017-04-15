package oasix.middleware.repository;

import java.util.List;

import org.springframework.data.elasticsearch.repository.ElasticsearchCrudRepository;

import oasix.middleware.model.ConnectionStats;

public interface ConnectionStatsRepository extends ElasticsearchCrudRepository<ConnectionStats, String> {
	List<ConnectionStats> findByHost(String host);
}