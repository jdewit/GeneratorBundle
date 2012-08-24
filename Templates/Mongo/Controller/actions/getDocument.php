    /**
     * Get {{ entityTitle }} by id.
     *
     */
    public function get{{ entity }}($id)
    {
        ${{ entityCC }} = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('{{ bundleName }}:{{ entity }}')
            ->find($id);

        if (!${{ entityCC }}) {
            throw $this->createNotFoundException('No {{ entityTitle }} found');
        }

        return ${{ entityCC }};
    }

